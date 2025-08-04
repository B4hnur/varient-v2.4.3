<?php

namespace App\Controllers;

use App\Models\RewardModel;

class EarningsController extends BaseAdminController
{
    protected $rewardModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        if ($this->generalSettings->reward_system_status != 1 || user()->reward_system_enabled != 1) {
            redirectToUrl(langBaseUrl());
            exit();
        }
        $this->rewardModel = new RewardModel();
        $this->perPage = 15;
    }

    /**
     * Author Earnings Page
     */
    public function authorEarnings()
    {
        $data['title'] = trans("my_earnings");
        $data['userSession'] = getUserSession();
        $data['pageViewsCounts'] = $this->rewardModel->getPageViewsCountByDate(user()->id);
        $data['payouts'] = $this->rewardModel->getUserPayouts(user()->id);
        $data['numberOfDays'] = date('t');
        if (empty($data['numberOfDays'])) {
            $data['numberOfDays'] = 30;
        }
        $data['today'] = date('d');

        echo view('admin/includes/_header', $data);
        echo view('admin/author-earnings/author_earnings', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Set Payout Account
     */
    public function setPayoutAccount()
    {
        $data['title'] = trans("set_payout_account");
        $data['userSession'] = getUserSession();
        $payout = inputGet('payout');
        if ($payout != 'paypal' && $payout != 'bitcoin' && $payout != 'iban' && $payout != 'swift') {
            if (payoutMethod('paypal_status')) {
                $payout = 'paypal';
            } elseif (payoutMethod('bitcoin_status')) {
                $payout = 'bitcoin';
            } elseif (payoutMethod('iban_status')) {
                $payout = 'iban';
            } elseif (payoutMethod('swift_status')) {
                $payout = 'swift';
            }
        }
        $data['selectedPayout'] = $payout;

        echo view('admin/includes/_header', $data);
        echo view('admin/author-earnings/set_payout_account', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Set Payout Account Post
     */
    public function setPayoutAccountPost()
    {
        if ($this->rewardModel->setPayoutAccount()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        redirectToBackURL();
    }

    /**
     * New Payout Request Post
     */
    public function newPayoutRequestPost()
    {
        $amount = inputPost('amount');
        $payoutMethod = inputPost('payout_method');
        //check active payouts
        if (!empty($this->rewardModel->getUserPayouts(user()->id, true))) {
            setErrorMessage("active_payment_request_error");
            redirectToBackUrl();
        }
        $minLimit = 0;
        if ($payoutMethod == 'paypal') {
            $minLimit = payoutMethod('paypal_min_amount');
        } elseif ($payoutMethod == 'bitcoin') {
            $minLimit = payoutMethod('bitcoin_min_amount');
        } elseif ($payoutMethod == 'iban') {
            $minLimit = payoutMethod('iban_min_amount');
        } elseif ($payoutMethod == 'swift') {
            $minLimit = payoutMethod('swift_min_amount');
        }
        if ($amount <= 0) {
            setErrorMessage("msg_error");
            redirectToBackUrl();
        }
        if ($amount < $minLimit) {
            setErrorMessage("invalid_withdrawal_amount");
            redirectToBackUrl();
        }
        if ($amount > user()->balance) {
            setErrorMessage("invalid_withdrawal_amount");
            redirectToBackUrl();
        }

        if ($this->rewardModel->addPayout(user(), $amount, 0)) {
            setSuccessMessage("msg_request_sent");
        } else {
            setErrorMessage("msg_error");
        }
        redirectToBackUrl();
    }
}
