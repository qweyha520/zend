<?php

class TransactioncountriesController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
  	session_start();  
		if(!isset($_SESSION['logged'])){
			$this->_redirect('index');
		}
    }

    public function indexAction()
    {
        $countries_data = new Application_Model_DbTable_Country(); 
		$data = $countries_data->getCountries();
		$this->view->countries = $data; 
		
		if($this->getRequest ()->getParam ( 'json') ==1){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->view->layout()->disableLayout();
			
			$term = $this->getRequest()->getParam('term');
			$_data=$countries_data->getCountriesListForSearch($term);
			$nArray = array();
			foreach ($_data as $row) {
			   $temp['id'] = $row['country_id'];
			   $temp['value'] = $row['country_name'];
			   $temp['label'] = $row['country_name'];
			   array_push($nArray, $temp);
			}

			echo $this->_helper->json($nArray);
			 
		} 
    }
	
    /*
     * insert a new account to database
     */
    public function addtoAccount(){
    	$account_data = new Application_Model_DbTable_Account(); 
    	$resutl = $account_data->addAccount("530240102168090", "2013-01-08 11:34:40", "369", "0211216199", "", "NZ", "EN", "ACUKLAND", "1357598084"); 
    	return $result; 
    }
    
	public function accountindexAction(){ 
		$result = $this->addtoAccount(); 
// 		echo $result;  
		
		$country = $this->getRequest()->getParam('country');   
		if(trim($country)!=''){
			$accounts_data = new Application_Model_DbTable_Account(); 
			$unknown_funding_data = new Application_Model_DbTable_FundingUnknown();  
			$page = $this->_request->getParam('page'); 
			// get account information by page 
			if (empty($page)) { 
				$page = 1; 
			}
			$paginator = $accounts_data->getOnePageOfAccountEntriesByLocale($page, $country); 
			  
			// get balance from FundingUnknown model; 
			$balance=0;
			$funding_data = new Application_Model_DbTable_FundingUnknown();
			$balance=$funding_data->getCurrentBalance(); 
			
			//pass values to view; 
			$this->view->balance = $balance; 
			$this->view->accounts = $paginator;
			$this->view->assign('country', $country);  	
		}
	} 
	
	public function accountviewAction() {
		$account = $this->getRequest()->getParam('accountnumber'); 
		 
		if (trim($account)!='') {
			
			$transaction_data = new Application_Model_DbTable_Transactions(); 
			$page = $this->_request->getParam('page'); 
			if (empty($page)) {
				$page = 1; 
			}
			
			$paginator = $transaction_data->getOnePageOfTransactionEntriesByAccount($page, $account);   
			$this->view->transactions = $paginator;
			$this->view->assign('account', $account); 
	
		} 
	}
	
	/*
	 * display country ledger information
	 */
	public function countryledgerAction(){ 
		$country = $this->getRequest()->getParam('country'); 

		if(trim($country)!=''){ 
			$unknow_finding_data = new Application_Model_DbTable_FundingUnknown(); 
			$transaction_data = new Application_Model_DbTable_Transactions(); 
			$unknow_funding_list = $unknow_finding_data->getAll(); 
			$paginator = $transaction_data->getTransactionByCountry($country); 
// 			echo '<pre>'; 
// 			print_r($unknow_funding_list); 
			
			$this->view->unknow_funding = $unknow_funding_list;     		
			$this->view->transactions = $paginator; 
			$this->view->assign('country', $country); 
		}
	}
	
	
	public function suspensefundingAction()
	{
		$funding_data = new Application_Model_DbTable_FundingUnknown();
		$data = $funding_data->getAll('date_added desc');
		$page = $this->_request->getParam('page');
		if (empty($page)) {
			$page = 1;
		}
		$paginator = $funding_data->getOnePageOfEntries($page);
		$this->view->fundings = $paginator;
	
		if($this->getRequest ()->getParam ( 'json') ==1){
			$this->_helper->viewRenderer->setNoRender(true);
			$this->view->layout()->disableLayout();
			$term = $this->getRequest()->getParam('term');
			$_data=$accounts_data->getForSearch($term);
			$nArray = array();
			foreach ($_data as $row) {
				$temp['id'] = $row['account_id'];
				$temp['value'] = $row['account_number'];
				$temp['label'] = $row['account_number'];
				array_push($nArray, $temp);
			}
	
			echo $this->_helper->json($nArray); 
				
		}
	} 
	
	public function suspensefundingviewAction()
	{
		$id = $this->_request->getParam('id');
		if(empty($id)) {
			$id = 0;
		}
	
		$suspense_funding = new Application_Model_DbTable_FundingUnknown();
		$data = $suspense_funding->get($id);
	
		$this->view->my_suspense_funding = $data;
			
	}
}

