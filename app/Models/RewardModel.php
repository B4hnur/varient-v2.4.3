<?php namespace App\Models;

use CodeIgniter\Model;

class RewardModel extends BaseModel
{
    protected $builderUsers;
    protected $builderPayouts;
    protected $builderGeneral;
    protected $builderPageviews;

    public function __construct()
    {
        parent::__construct();
        $this->builderUsers = $this->db->table('users');
        $this->builderPayouts = $this->db->table('payouts');
        $this->builderGeneral = $this->db->table('general_settings');
        $this->builderPageviews = $this->db->table('post_pageviews_month');
    }

    //get page views counts by date
    public function getPageViewsCountByDate($userId)
    {
        return $this->builderPageviews->select('COUNT(id) AS count, SUM(reward_amount) as total_amount, DATE(created_at) AS date')
            ->where('post_user_id', clrNum($userId))->where('reward_amount > 0')->where('MONTH(created_at)', date('m'))->groupBy('date')->get()->getResult();
    }

    //update settings
    public function updateSettings()
    {
        $submit = inputPost('submit');
        if ($submit == 'verification') {
            $vars = [
                'status' => inputPost('status'),
                'time_spent' => inputPost('time_spent'),
                'mouse' => inputPost('mouse'),
                'scroll' => inputPost('scroll')
            ];
            $data['human_verification'] = serialize($vars);
        } else {
            $amount = inputPost('reward_amount');
            if ($amount < 0.01) {
                $amount = 0.01;
            }
            $data = [
                'reward_system_status' => inputPost('reward_system_status'),
                'reward_amount' => $amount
            ];
        }
        if (!empty($data)) {
            return $this->builderGeneral->where('id', 1)->update($data);
        }
        return false;
    }

    //update payout methods
    public function updatePayoutMethods()
    {
        $post = [
            'paypal_status' => !empty(inputPost('paypal_status')) ? 1 : 0,
            'paypal_min_amount' => !empty(inputPost('paypal_min_amount')) ? inputPost('paypal_min_amount') : 0,
            'bitcoin_status' => !empty(inputPost('bitcoin_status')) ? 1 : 0,
            'bitcoin_min_amount' => !empty(inputPost('bitcoin_min_amount')) ? inputPost('bitcoin_min_amount') : 0,
            'iban_status' => !empty(inputPost('iban_status')) ? 1 : 0,
            'iban_min_amount' => !empty(inputPost('iban_min_amount')) ? inputPost('iban_min_amount') : 0,
            'swift_status' => !empty(inputPost('swift_status')) ? 1 : 0,
            'swift_min_amount' => !empty(inputPost('swift_min_amount')) ? inputPost('swift_min_amount') : 0
        ];
        $data = [
            'payout_methods' => serialize($post)
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update currency
    public function updateCurrency()
    {
        $data = [
            'currency_name' => inputPost('currency_name'),
            'currency_symbol' => inputPost('currency_symbol'),
            'currency_format' => inputPost('currency_format'),
            'currency_symbol_format' => inputPost('currency_symbol_format')
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //get earnings count
    public function getEarningsCount()
    {
        $this->filterEarnings();
        return $this->builderUsers->countAllResults();
    }

    //get earnings paginated
    public function getEarningsPaginated($perPage, $offset)
    {
        $this->filterEarnings();
        return $this->builderUsers->orderBy('balance DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //earnings filter
    public function filterEarnings()
    {
        $q = cleanStr(inputGet('q'));
        if (!empty($q)) {
            $this->builderUsers->groupStart()->like('users.username', $q)->orLike('users.email', $q)->groupEnd();
        }
        $this->builderUsers->groupStart()->where('reward_system_enabled', 1)->orWhere('balance >', 0)->groupEnd();
    }

    //get payout
    public function getPayout($id)
    {
        return $this->builderPayouts->where('id', clrNum($id))->get()->getRow();
    }

    //get payouts count
    public function getPayoutsCount()
    {
        $this->filterPayouts();
        return $this->builderPayouts->countAllResults();
    }

    //get payouts paginated
    public function getPayoutsPaginated($perPage, $offset)
    {
        $this->filterPayouts();
        return $this->builderPayouts->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get user payouts
    public function getUserPayouts($userId, $onlyActive = false)
    {
        if($onlyActive){
            $this->builderPayouts->where('status', 0);
        }
        return $this->builderPayouts->where('user_id', clrNum($userId))->orderBy('id DESC')->get()->getResult();
    }

    //payouts filter
    public function filterPayouts()
    {
        $q = cleanStr(inputGet('q'));
        if (!empty($q)) {
            $this->builderPayouts->groupStart()->like('username', $q)->orLike('email', $q)->orLike('payout_method', $q)->groupEnd();
        }
    }

    //add payout
    public function addPayout($user, $amount, $status)
    {
        if (!empty($user)) {
            $data = [
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'payout_method' => inputPost('payout_method'),
                'amount' => $amount,
                'status' => !empty($status) ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
            if ($this->builderPayouts->insert($data)) {
                if ($data['status'] == 1) {
                    $this->updateUserBalance($user->id, $amount);
                }
                return true;
            }
        }
        return false;
    }

    //approve payout
    public function approvePayout($payoutId)
    {
        $payout = $this->getPayout($payoutId);
        if(!empty($payout)){
            $user = getUserById($payout->user_id);
            if($this->builderPayouts->where('id', $payout->id)->update(['status' => 1])){
                $this->updateUserBalance($user->id, $payout->amount);
            }
            return true;
        }
        return false;
    }

    //update user balance
    public function updateUserBalance($userId, $amount)
    {
        $user = getUserById($userId);
        if (!empty($user) && $amount > 0) {
            $balance = $user->balance - $amount;
            if ($balance < 0) {
                $balance = 0;
            }
            return $this->builderUsers->where('id', $user->id)->update(['balance' => $balance]);
        }
        return false;
    }

    //delete payout
    public function deletePayout($id)
    {
        $payout = $this->getPayout($id);
        if (!empty($payout)) {
            return $this->builderPayouts->where('id', $payout->id)->delete();
        }
        return false;
    }

    //get user payouts count
    public function getUserPayoutsCount($userId)
    {
        return $this->builderPayouts->where('user_id', clrNum($userId))->countAllResults();
    }

    //get paginated user payouts
    public function getUserPayoutsPaginated($userId, $perPage, $offset)
    {
        return $this->builderPayouts->where('user_id', clrNum($userId))->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //set payout account
    public function setPayoutAccount()
    {
        if (authCheck()) {
            $submit = inputPost('submit');
            $payout = user()->payout_methods;
            if (!empty($payout)) {
                $payout = unserializeData($payout);
            }
            if (empty($payout)) {
                $payout = [];
            }
            if ($submit == 'paypal') {
                $payout['paypal_email'] = inputPost('paypal_email');
            } elseif ($submit == 'bitcoin') {
                $payout['btc_address'] = inputPost('btc_address');
            } elseif ($submit == 'iban') {
                $payout['iban_full_name'] = inputPost('iban_full_name');
                $payout['iban_country'] = inputPost('iban_country');
                $payout['iban_bank_name'] = inputPost('iban_bank_name');
                $payout['iban_number'] = inputPost('iban_number');
            } elseif ($submit == 'swift') {
                $payout['swift_full_name'] = inputPost('swift_full_name');
                $payout['swift_address'] = inputPost('swift_address');
                $payout['swift_state'] = inputPost('swift_state');
                $payout['swift_city'] = inputPost('swift_city');
                $payout['swift_postcode'] = inputPost('swift_postcode');
                $payout['swift_country'] = inputPost('swift_country');
                $payout['swift_bank_account_holder_name'] = inputPost('swift_bank_account_holder_name');
                $payout['swift_iban'] = inputPost('swift_iban');
                $payout['swift_code'] = inputPost('swift_code');
                $payout['swift_bank_name'] = inputPost('swift_bank_name');
                $payout['swift_bank_branch_city'] = inputPost('swift_bank_branch_city');
                $payout['swift_bank_branch_country'] = inputPost('swift_bank_branch_country');
            }
            $data = [
                'payout_methods' => serialize($payout)
            ];
            return $this->builderUsers->where('id', clrNum(user()->id))->update($data);
        }
        return false;
    }

    //get paginated pageviews count
    public function getPageviewsCount()
    {
        $this->filterPageviews();
        return $this->builderPageviews->join('users', 'users.id = post_pageviews_month.post_user_id')
            ->select('post_pageviews_month.*, users.username AS author_username, users.slug AS author_slug')->countAllResults();
    }

    //get paginated pageviews
    public function getPageviewsPaginated($perPage, $offset)
    {
        $this->filterPageviews();
        return $this->builderPageviews->join('users', 'users.id = post_pageviews_month.post_user_id')
            ->select('post_pageviews_month.*, users.username AS author_username, users.slug AS author_slug')->orderBy('id DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //pageviews filter
    public function filterPageviews()
    {
        $q = cleanStr(inputGet('q'));
        if (!empty($q)) {
            $this->builderPageviews->groupStart()->like('users.username', $q)->orLike('ip_address', $q)->groupEnd();
        }
    }

    //enable disable reward system
    public function enableDisableRewardSystem($user)
    {
        if (!empty($user)) {
            if ($user->reward_system_enabled == 1) {
                $data['reward_system_enabled'] = 0;
            } else {
                $data['reward_system_enabled'] = 1;
            }
            return $this->db->table('users')->where('id', $user->id)->update($data);
        }
        return false;
    }
}