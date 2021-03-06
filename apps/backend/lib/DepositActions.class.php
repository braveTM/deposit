<?php 

/**
 * @package apps\backend\lib
 */

/**
 * DepositAction extends sfActions and executes all the logic for the current request.
 * 
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     brave.cheng<brave.cheng@expacta.com.cn>
 * @version    SVN: $Id: sfActions.class.php 19911 2009-07-06 07:52:48Z FabianLange $
 */

class DepositActions extends sfActions
{


    public $tag                 = '&';
    public $uri                 = '';

    /**
     * Execute product page parameters
     *
     * @return void
     *
     * @issue 2579
     */
    protected function productParameters() {
        $this->productName      = $this->getRequestParameter('productName');
        $this->productBankName  = $this->getRequestParameter('productBankName');
        $this->commonParameters();
    }


    /**
     * Execute common page parameters
     *
     * @return void
     *
     * @issue 2553
     */
    protected function commonParameters() {
        $this->sort        = $this->getRequestParameter('sort');
        $this->sortBy      = $this->getRequestParameter('sortBy');
        $this->pager       = $this->getRequestParameter('pager');
        $this->uriSid = $this->sid = $this->getRequestParameter('sid');
        
        if ($this->sid && !is_numeric($this->sid)) {
            $this->sid = 'null';
        }
    }

    /**
     * Execute bank page parameters
     *
     * @return void
     *
     * @issue 2553
     */
    protected function bankParameters() {
        $this->sname = $this->getRequestParameter('sname');
        $this->commonParameters();
    }   

    /**
     * Get feedback parameters
     *
     * @return void
     *
     * @issue 2579
     */
    protected function feedbackParameters() {
        $this->sEmail       = $this->getRequestParameter('semail');
        $this->sNickname    = $this->getRequestParameter('snickname');
        $this->commonParameters();
    }

    /**
     * Get query sql order 
     *
     * @param string $sql    sql string
     * @param string $pk     primary key string
     * @param array  $ignore ignore array
     *
     * @return string
     *
     * @issue 2579
     */
    protected function querySqlBySort($sql, $pk = '', $ignore = array(), $andOrder = '') {
        if ($this->sortBy) {
            array_push($ignore, $pk);
            if (in_array($this->sortBy, $ignore)) {
                $sql = sprintf(' ORDER BY %s %s', $this->sortBy, $this->sort);    
            } else {
                $sql = sprintf(' ORDER BY CONVERT(%s USING gbk) %s', $this->sortBy, $this->sort);
            }
        } else {
            $sql = sprintf(' ORDER BY %s DESC', $pk);
        }

        if ($andOrder) {
            $sql .= ", " . $andOrder;
        }

        return $sql;
    }

    /**
     * Create filter sql for expected rate
     *
     * @param string $tableField table field
     *
     * @return void
     *
     * @issue 2678
     */
    public function createExpectedRateFilterSql($tableField) {
        $expectedRateLists = PushDevicesPeer::getExpectedYields();
        $queryExpectedRate = $expectedRateLists[$this->sExpectedRate];

        if (!$queryExpectedRate) {
            $this->forward404();
        }
        if ($queryExpectedRate[0] !== '' &&  $queryExpectedRate[1] !== '') {
            return sprintf(' AND ( %s < %s AND %s <= %s)', $queryExpectedRate[0], $tableField, $tableField, $queryExpectedRate[1]);
        }
        if ($queryExpectedRate[0] !== '' && $queryExpectedRate[1] === '') {
            return sprintf(' AND ( %s > %s)', $tableField, $queryExpectedRate[0]);
        }
        if ($queryExpectedRate[0] === '' && $queryExpectedRate[0] !== '') {
            return sprintf(' AND ( %s <= %s)', $tableField, $queryExpectedRate[1]);
        }
    }

    /**
     * Create filter sql for amount
     *
     * @param string $tableField table field
     *
     * @return void
     *
     * @issue 2678
     */
    public function createAmountFilterSql($tableField) {
        $amountLists = DepositFinancialProductsPeer::getSearchSaleStartAmount();
        $queryAmount = $amountLists[$this->sAmount];
        if (!$queryAmount) {
            $this->forward404();
        }

        if ($queryAmount[0] !== '' &&  $queryAmount[1] !== '') {
            return sprintf(' AND ( %s < %s AND %s <= %s)', $queryAmount[0], $tableField, $tableField, $queryAmount[1]);
        }
        if ($queryAmount[0] !== ''  && $queryAmount[1] === '') {
            return sprintf(' AND ( %s > %s)', $tableField, $queryAmount[0]);
        }
        if ($queryAmount[0] === '' && $queryAmount[1] !== '') {
            return sprintf(' AND ( %s <= %s)', $tableField, $queryAmount[1]);
        }
    }

    /**
     * Verify abnormal operation
     *
     * @return void
     *
     * @issue 2579
     */
    public function verifyAbnormalOperation() {
        if (!$this->hasRequestParameter('act')) {
            $this->forward404();
        }
        switch ($this->getRequestParameter('act')) {
            case 'edit':
                if (!$this->hasRequestParameter('id') || $this->getRequestParameter('id') == '') {
                    $this->forward404();
                }
                break;
        }
    }

    

}