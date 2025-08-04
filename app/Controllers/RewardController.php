<?php

namespace App\Controllers;

use App\Models\RewardModel;

class RewardController extends BaseAdminController
{
    protected $rewardModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        if (!hasPermission('reward_system')) {
            redirectToUrl(adminUrl());
            exit();
        }
        $this->rewardModel = new RewardModel();
    }

    /**
     * Reward System
     */
    public function rewardSystem()
    {
        $data['title'] = trans("reward_system");
        $data['userSession'] = getUserSession();

        echo view('admin/includes/_header', $data);
        echo view('admin/reward/reward_system', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Update Settings Post
     */
    public function updateSettingsPost()
    {
        if ($this->rewardModel->updateSettings()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('reward-system'));
    }

    /**
     * Update Payout Post
     */
    public function updatePayoutPost()
    {
        if ($this->rewardModel->updatePayoutMethods()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('reward-system'));
    }

    /**
     * Update Currency Post
     */
    public function updateCurrencyPost()
    {
        if ($this->rewardModel->updateCurrency()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('reward-system'));
    }

    /**
     * Earnings
     */
    public function earnings()
    {
        $data['title'] = trans("earnings");
        $numRows = $this->rewardModel->getEarningsCount();
        $data['pager'] = paginate($this->perPage, $numRows);
        $data['userSession'] = getUserSession();
        $data['earnings'] = $this->rewardModel->getEarningsPaginated($this->perPage, $data['pager']->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/reward/earnings', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Payouts
     */
    public function payouts()
    {
        $data['title'] = trans("payouts");
        $numRows = $this->rewardModel->getPayoutsCount();
        $data['pager'] = paginate($this->perPage, $numRows);
        $data['userSession'] = getUserSession();
        $data['payouts'] = $this->rewardModel->getPayoutsPaginated($this->perPage, $data['pager']->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/reward/payouts', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Payout
     */
    public function addPayout()
    {
        $data['title'] = trans("add_payout");

        echo view('admin/includes/_header', $data);
        echo view('admin/reward/add_payout', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Payout Post
     */
    public function addPayoutPost()
    {
        $userId = inputPost('user_id');
        $amount = inputPost('amount');
        $user = getUserById($userId);
        if (!empty($user)) {
            if ($user->balance < $amount) {
                setErrorMessage("insufficient_balance");
            } else {
                if ($this->rewardModel->addPayout($user, $amount, 1)) {
                    setSuccessMessage("msg_payout_added");
                } else {
                    setErrorMessage("msg_error");
                }
            }
        }
        return redirect()->to(adminUrl('reward-system/add-payout'));
    }

    /**
     * Approve Payout Post
     */
    public function approvePayoutPost()
    {
        $id = inputPost('id');
        if ($this->rewardModel->approvePayout($id)) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        redirectToBackURL();
    }

    /**
     * Delete Payout Post
     */
    public function deletePayoutPost()
    {
        $id = inputPost('id');
        if ($this->rewardModel->deletePayout($id)) {
            setSuccessMessage("msg_deleted");
        } else {
            setErrorMessage("msg_error");
        }
    }

    /**
     * Pageviews
     */
    public function pageviews()
    {
        $data['title'] = trans("pageviews");
        $numRows = $this->rewardModel->getPageviewsCount();
        $data['pager'] = paginate($this->perPage, $numRows);
        $data['userSession'] = getUserSession();
        $data['pageviews'] = $this->rewardModel->getPageviewsPaginated($this->perPage, $data['pager']->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/reward/pageviews', $data);
        echo view('admin/includes/_footer');
    }
}
