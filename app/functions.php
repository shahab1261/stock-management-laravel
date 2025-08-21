<?php

require_once 'crud.php';
date_default_timezone_set("Asia/Karachi");
class Functions
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
        $this->db->connect();
    }

    function CheckEmailExists($email)
    {
        $sql = "SELECT * from users WHERE email='$email'";
        $this->db->sql($sql);
        $res = $this->db->numRows();
        if ($res > 0) {
            return true;
        } else {
            return false;
        }
    }//checkEmail Exist
    function checktokenexist($token)
    {
        $sql = "SELECT * from users WHERE reset_token='$token'";
        $this->db->sql($sql);
        $res = $this->db->numRows();
        // echo $res;
        if ($res > 0) {
            return true;
        } else {
            return false;
        }
    }//checktokenexist
    //generat a random password
    function random_Code()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 6; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $pass1 = implode($pass);//turn the array into a string
        if ($this->checktokenexist($pass1)) {
            random_Code();
        } else {
            return $pass1;
        }


    }//generat a random password
    function sendrecoveremail($email, $pass)
    {
        include_once "../phpmailer/sendmailfunction.php";
        $link = '<a href="http://localhost/complainbrokers/resetpassword?resetpsw='. $pass.'">Reset your Password</a>';
        $to = $email;
        $message = "You have requested to reset the password, please click on below link to reset the password. <br>" . $link . "<br><br> Thank you!";
        $subject = "Password recovery";
        sendemailsmtp($to, $message, $subject);

    }//sendrecoveremail
    function UpdatePassword($userid, $confirmpass, $oldpass)
    {
        if ($this->CheckOldPass($userid, $oldpass)) {
            $hashPassword = md5($confirmpass);

            $sql = "update users set password='$hashPassword' where id='$userid'";
            if ($this->db->sql($sql)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }//UpdatePassword
    function CheckOldPass($userid, $oldpass)
    {
        $hashPassword = md5($oldpass);
        $sql = "SELECT password from users where id='$userid' and password='$hashPassword'";
        $this->db->sql($sql);
        $res = $this->db->numRows();
        if ($res > 0) {
            return true;
        } else {
            return false;
        }
    }//CheckOldPass
    function getSingleEmployee($id,$user_type)
    {
        $sql = "select * from users where id='$id' and user_type='$user_type'";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }//getSingleEmployee
    function getAllEmployee($user_type)
    {
        $sql = "select * from users where user_type='$user_type' order by id desc ";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }//getAllEmployee
    function getSingleCustomer($id)
    {

        $sql = "select * from customers where id='$id' ";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
//        echo $this->db->getSql();
    }//getSingleCustomer
    function getAllCustomers($status = "")
    {
        //1 = active, 0 = non-active
        $query=' where 1=1 ';

        if($status!=''){
            $query.=" and status = '$status'";
        }
        $sql = "select * from customers ".$query." order by id desc  ";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }//getAllCustomers
    function getSingleSupplier($id)
    {
        $sql = "select * from suppliers where id='$id'";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }//getSingleSupplier
    function getAllSuppliers($status = "")
    {
        //1 = active, 0 = non-active
        $query=' where 1=1 ';

        if($status!=''){
            $query.=" and status = '$status'";
        }
        $sql = "select * from suppliers ".$query." order by id desc  ";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }//getAllSuppliers

    function getjournal()
    {
        $sql = "select * from journal_new order by id desc";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }//get journal enteries

    function getjournalenteriesbyfilter($vendor_id="",$start_date="",$end_date="",$vendor_type=""){
        $query='where 1=1';
        $current_date=date('Y-m-d');

        if($vendor_id!='' && $vendor_type!=''){
            $query.=" and vendor_id='$vendor_id'and vendor_type='$vendor_type'";
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }

        $sql="SELECT * FROM `journal_new` ".$query;
        
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getvendorbytype($vendortype, $vendorid){
            //1=supplier,2=customer,3=product,4=expense,5=income,6=bank,7=cash,8=mp, vendor types
            if($vendortype=="1" || $vendortype == 1){
                $vendor_details = $this->getSingleSupplier($vendorid);
                $obj = new stdClass();
                $obj->vendor_details = $vendor_details;
                $obj->vendor_name = $vendor_details[0]['name'];
                $obj->vendor_type = "supplier";
                return $obj;
            }else if($vendortype=="2" || $vendortype == 2){
                $vendor_details = $this->getSingleCustomer($vendorid);
                $obj = new stdClass();
                $obj->vendor_details = $vendor_details;
                $obj->vendor_name = $vendor_details[0]['name'];
                $obj->vendor_type = "customer";
                return $obj;
            }else if($vendortype=="3" || $vendortype == 3){
                $vendor_details = $this->getSingleProduct($vendorid);
                $obj = new stdClass();
                $obj->vendor_details = $vendor_details;
                $obj->vendor_name = $vendor_details[0]['name'];
                $obj->vendor_type = "product";
                return $obj;
            }else if($vendortype=="4" || $vendortype == 4){
                $vendor_details = $this->getSingleExpenses($vendorid);
                $obj = new stdClass();
                $obj->vendor_details = $vendor_details;
                $obj->vendor_name = $vendor_details[0]['expense_name'];
                $obj->vendor_type = "expense";
                return $obj;
            }else if($vendortype=="5" || $vendortype == 5){
                $vendor_details = $this->getSingleIncome($vendorid);
                $obj = new stdClass();
                $obj->vendor_details = $vendor_details;
                $obj->vendor_name = $vendor_details[0]['income_name'];
                $obj->vendor_type = "income";
                return $obj;
            }else if($vendortype=="6" || $vendortype == 6){
                $vendor_details = $this->getSingleBank($vendorid);
                $obj = new stdClass();
                $obj->vendor_details = $vendor_details;
                $obj->vendor_name = $vendor_details[0]['name'];
                $obj->vendor_type = "bank";
                return $obj;
            }else if($vendortype=="7" || $vendortype == 7){
//                $vendor_details = $this->getSingleSupplier($vendorid);
                $obj = new stdClass();
                $obj->vendor_details = array();
                $obj->vendor_name = "cash";
                $obj->vendor_type = "cash";
                return $obj;
            }else if($vendortype=="8" || $vendortype == 8){
//                $vendor_details = $this->getSingleSupplier($vendorid);
                $obj = new stdClass();
                $obj->vendor_details = array();
                $obj->vendor_name = "MP";
                $obj->vendor_type = "MP";
                return $obj;
            }else if($vendortype=="9" || $vendortype == 9){
               $vendor_details = $this->getSingleEmployee($vendorid,3);//3 is employee type
                $obj = new stdClass();
                $obj->vendor_details = $vendor_details;
                $obj->vendor_name = $vendor_details[0]['name'];
                $obj->vendor_type = "Employee";
                return $obj;
            }

    }
    function getSingleProduct($id)
    {

        $sql = "select * from products where id='$id' ";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
//        echo $this->db->getSql();
    }//getSingleProduct
    function getAllProduct()
    {
        $sql = "select * from products order by id desc";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }//getAllProduct
    function getAlldippableProduct()
    {
        $sql = "select * from products where is_dippable = 1 order by id desc";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }//getAllProduct

    function getAllNozleProduct()
    {
        $sql = "SELECT DISTINCT(n.product_id) as id, p.name as name FROM `nozzle` n join products p on n.product_id=p.id;";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }//getAllProduct

    function getgeneralproducts(){
        $sql = "SELECT p.id,p.name,p.current_sale, p.tank_id FROM `products` p
                LEFT JOIN `nozzle` n ON p.id = n.product_id
                WHERE n.product_id IS NULL;";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }

    }

    function getSingleTankLari($id)
    {
        $sql = "select * from tank_lari where id='$id' ";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
//        echo $this->db->getSql();
    }//getSingleTankLari

    function getCustomerTanklari($customer_id)
    {
        $sql = "select * from tank_lari where customer_id='$customer_id' and tank_type = '3'";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
//        echo $this->db->getSql();
    }
    function getAllTankLari($type)
    {
        $sql = "select * from tank_lari where tank_type='$type' order by id desc";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }//getAllTankLari
    function getSingleBank($id)
    {
        $sql = "select * from banks where id='$id' ";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
//        echo $this->db->getSql();
    }//getSingleBank
    function getAllBanks($status="")
    {
        //1 = active, 0 = non-active
        $query=' where 1=1 ';

        if($status!=''){
            $query.=" and status = '$status'";
        }

        $sql = "select * from banks ".$query." order by id desc";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }//getAllBanks
    function getSingleTerminal($id)
    {
        $sql = "select * from terminals where id='$id' ";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
//        echo $this->db->getSql();
    }//getSingleTerminal
    function getAllTerminals()
    {
        $sql = "select * from terminals order by id desc";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }//getAllTerminals
    function getSinglePurchase($id)
    {
        $sql = "select * from purchase where id='$id' ";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
//        echo $this->db->getSql();
    }//getSinglePurchase
    function getAllPurchase()
    {
        $sql = "select * from purchase order by id desc";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }//getAllPurchases

    function UserInfo($user_id){
        $sql = "select * from users where id='$user_id' ";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }

    function getAllSales()
    {
        $sql = "select * from sales order by id desc";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }


    function getAllDrivers()
    {
        $sql = "select * from drivers order by id desc";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }

    function getSingleDriver($id){
        $sql="select * from drivers where id='$id'";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }


    function getLastColumnValue($table,$id){
        $sql="select * from `$table` ORDER BY ".$id." DESC LIMIT 1";
        if ($this->db->sql($sql)) {
           $result=$this->db->getResult();
           if(!empty($result)){
               return $result[0][$id];
           }
        }
    }

    function getAllProductCategories(){
        $sql="select * from product_categories";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getSingleProductCategory($cid){
        $sql="select * from product_categories where pc_id='$cid'";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getAllTanks(){
        $sql="select * from tanks";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getAlldippableTanks(){
        $sql="select * from tanks where is_dippable = 1";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function gettankswithoutproducts(){
        $sql="select * from tanks where product_id = '-1'";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getSingleTanks($tid){
        $sql="select * from tanks where id='$tid'";
        // echo $sql;
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

     function gettanksbyproductid($product_id){
        $sql="select * from tanks where product_id='$product_id'";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }


    function checkDipChartInfoExist($tank_id){
        $sql="select * from dip_charts where tank_id='$tank_id'";
        if($this->db->sql($sql)){
            return $this->db->numRows();
        }
    }

    function getTankDips($tank_id){
        $sql="select * from dip_charts where tank_id='$tank_id'";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }


//    function getPurchaseChamber($pid){
//            $sql="SELECT * FROM `purchase_chambers` WHERE purchase_id='$pid'";
//        if($this->db->sql($sql)){
//            return $this->db->getResult();
//        }
//    }


    function getPurchaseChambers($pid){
        $sql="SELECT * FROM `purchase_chambers_details` where purchase_id='$pid'";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }
    function getDecent($pid){
        $sql="SELECT * FROM `decentations` INNER JOIN tanks ON decentations.to=tanks.id INNER JOIN products on decentations.type=products.id WHERE decentations.purchase_id='$pid'";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getSupplierDrivers($sid){
        $sql="SELECT * FROM `tank_lari` WHERE supplier_id='$sid'";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getAllDippers(){
        $sql="SELECT * FROM `dippers`";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getSingleDippers($did){
        $sql="SELECT * FROM `dippers` WHERE dipper_id='$did'";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getAllTransactions($vendor_id,$start_date,$end_date,$transaction_type = "", $payment_type = "",$vendor_type=""){
        $query='where 1=1';
        $current_date=date('Y-m-d');

        if($vendor_id!='' && $vendor_type!=''){
            $query.=" and vendor_id='$vendor_id'and vendor_type='$vendor_type'";
        }

        if($transaction_type!=''){
            $query.=" and transaction_type='$transaction_type'";
        }
        if($payment_type!=''){
            $query.=" and payment_type='$payment_type'";
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `transactions` ".$query;
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }
    function get_ledger_purchase_type($transaction_type, $payment_type){
        // transaction_type 1 = receiving, 2 = payment
        // payment_type 1 = cash, 2 = bank

        //12 = credit sales type in ledger, hard coded on credit_sale_table.php
        if($transaction_type==1 && $payment_type == 1){
            //cash receiving in ledger
            return 8;
        }else if($transaction_type==2 && $payment_type == 1){
            //cash payments in ledger
            return 9;
        }else if($transaction_type==1 && $payment_type == 2){
            //bank receiving in ledger
            return 7;
        }else if($transaction_type==2 && $payment_type == 2){
            //bank receiving in ledger
            return 3;
        }

    }


    function SearchSales($product_id,$customer_id,$start_date="",$end_date="",$vendor_id="",$vendor_type="",$transport_id="")
    {
        $current_date=date('Y-m-d');
        $query='where 1=1';
        if($product_id!=''){
            $query.=" and product_id='$product_id'";
        }

//        if($customer_id!=''){
//            $query.=" and customer_id='$customer_id'";
//        }
        if($vendor_id!='' && $vendor_type!=''){
            $query.=" and customer_id='$vendor_id'and vendor_type='$vendor_type'";
        }
        if($transport_id != ''){
            $query .= " and tank_lari_id = '$transport_id' ";
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(create_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(create_date) BETWEEN '$current_date' AND '$current_date'";
        }


        $sql = "select * from sales ".$query." order by id desc";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }
    function SearchCreditSales($product_id,$customer_id,$start_date="",$end_date="",$vendor_id="",$vendor_type="",$transport_id="")
    {
        $current_date=date('Y-m-d');
        $query='where 1=1';
        if($product_id!=''){
            $query.=" and product_id='$product_id'";
        }

//        if($customer_id!=''){
//            $query.=" and customer_id='$customer_id'";
//        }
        if($vendor_id!='' && $vendor_type!=''){
            $query.=" and vendor_id='$vendor_id'and vendor_type='$vendor_type'";
        }
        if($transport_id != ''){
            $query .= " and vehicle_id = '$transport_id' ";
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transasction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transasction_date) BETWEEN '$current_date' AND '$current_date'";
        }


        $sql = "select * from credit_sales ".$query." order by id desc";
//        echo $sql;
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }
    function SearchCreditSalesGroupBy($product_id,$customer_id,$start_date="",$end_date="",$vendor_id="",$vendor_type="",$transport_id="")
    {
        $current_date=date('Y-m-d');
        $query='where 1=1';
        if($product_id!=''){
            $query.=" and product_id='$product_id'";
        }

        if($vendor_id!='' && $vendor_type!=''){
            $query.=" and vendor_id='$vendor_id'and vendor_type='$vendor_type'";
        }
        if($transport_id != ''){
            $query .= " and vehicle_id = '$transport_id' ";
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transasction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transasction_date) BETWEEN '$current_date' AND '$current_date'";
        }

        $sql = "select *, sum(quantity) as stocksum, sum(amount) as amountsum from credit_sales ".$query." group by vehicle_id order by id desc";

        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }
    function deleterecords( $vendortype="", $vendorid=""){
        $sql = "delete from transactions where vendor_type = '$vendortype' and vendor_id = '$vendorid'";
        $this->db->sql($sql);

        $sql = "delete from sales where vendor_type = '$vendortype' and customer_id = '$vendorid'";
        $this->db->sql($sql);

        $sql = "delete from purchase where vendor_type = '$vendortype' and supplier_id = '$vendorid'";
        $this->db->sql($sql);

        $sql = "delete from ledger where vendor_type = '$vendortype' and vendor_id = '$vendorid'";
        $this->db->sql($sql);

        $sql = "delete from journal_new where vendor_type = '$vendortype' and vendor_id = '$vendorid'";
        $this->db->sql($sql);

        $sql = "delete from credit_sales where vendor_type = '$vendortype' and vendor_id = '$vendorid'";
        $this->db->sql($sql);
    }

    function SearchPurchase($supplier_id="",$product_id="",$start_date="",$end_date="",$vendor_id="",$vendor_type="",$transport_id="")
    {
        $current_date=date('Y-m-d');
        $query='where 1=1';
        if($supplier_id!=''){
//            $query.=" and supplier_id='$supplier_id'";
        }

        if($vendor_id!='' && $vendor_type!=''){
            $query.=" and supplier_id='$vendor_id'and vendor_type='$vendor_type'";
        }

        if($product_id!=''){
            $query.=" and product_id='$product_id'";
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(purchase_date) BETWEEN '$start_date' AND '$end_date' ";
        }else{
            $query.=" and DATE(purchase_date) BETWEEN '$current_date' AND '$current_date' ";
        }
        if($transport_id != ''){
            $query .= " and vehicle_no = '$transport_id' ";
        }

        $sql = "select * from purchase ".$query.' order by id desc';

        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }//getAllPurchases


//    function getCustomerBalance($cid){
//        $sql="select balance from customers where id='$cid'";
//        if($this->db->sql($sql)){
//            return $this->db->getResult();
//        }
//    }
//
//    function getSupplierBalance($sid){
//        $sql="select opening_balance as balance from suppliers where id='$sid'";
//        if($this->db->sql($sql)){
//            return $this->db->getResult();
//        }
//    }

//    function getProductBalance($pid){
//        $sql="select product_amount as balance from products where id='$pid'";
//        if($this->db->sql($sql)){
//            return $this->db->getResult();
//        }
//    }


    function getAllLedger($product_id,$start_date,$end_date){
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($product_id!=''){
            $query.=" and vendor_type='3' and vendor_id='$product_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query." order by transaction_date asc";;
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getSupplierLedger($supplier_id,$start_date,$end_date){
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($supplier_id!=''){
            $query.=" and vendor_type='1' and vendor_id='$supplier_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query." order by transaction_date asc";;
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getCustomerLedger($customer_id,$start_date,$end_date){
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($customer_id!=''){
            $query.=" and vendor_type='2' and vendor_id='$customer_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query." order by transaction_date asc";;
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getBankLedger($bank_id,$start_date,$end_date){
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($bank_id!=''){
            $query.=" and vendor_type='6' and vendor_id='$bank_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query." order by transaction_date asc";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getEmployeeLedger($emp_id,$start_date,$end_date){
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($emp_id!=''){
            $query.=" and vendor_type='9' and vendor_id='$emp_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query." order by transaction_date asc";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getCashLedger($start_date,$end_date){
        $query="where 1=1 and vendor_type='7'";
        $current_date=date('Y-m-d');

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query." order by transaction_date asc";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getMpledger($start_date,$end_date){
        $query="where 1=1 and vendor_type='8'";
        $current_date=date('Y-m-d');

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query." order by transaction_date asc";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getIncomeLedger($income_id,$start_date,$end_date){
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($income_id!=''){
            $query.=" and vendor_type='5' and vendor_id='$income_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query." order by transaction_date asc";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getExpenseLedger($expense_id,$start_date,$end_date){
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($expense_id!=''){
            $query.=" and vendor_type='4' and vendor_id='$expense_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query." order by transaction_date asc";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getSingleSales($sid)
    {
        $sql = "select * from sales where id='$sid'";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }

    function calculateProfit($pid){
        $sales=$this->getSingleSales($pid);
        $sales_product_id=$sales[0]['product_id'];
        $sale_id=$sales[0]['id'];
        $sale_quantity=$sales[0]['quantity'];
        $purchase_query="select * from purchase where product_id='$sales_product_id' and CAST(sold_quantity AS UNSIGNED) < CAST(stock AS UNSIGNED) order by purchase_date asc";
        if($this->db->sql($purchase_query)){
            $purchase_result=$this->db->getResult();
       
            $profit_arr=[];
            $total_profit=0;
            foreach ($purchase_result as $purchase_row){
                
                $available_stock=$purchase_row['stock']-$purchase_row['sold_quantity'];
                if($purchase_row['stock'] > $purchase_row['sold_quantity']){
                    if($sale_quantity<=$available_stock){
                        $purchase_price=$sale_quantity*$purchase_row['rate_adjustment'];
                        $sale_price=$sales[0]['rate']*$sale_quantity;

                        $profit_price=$sale_price-$purchase_price;
                        $profit_arr[]=$profit_price;
                        $total_profit+=$profit_price;
                        //update sale product profit
                        $update_sale_product_profit="update sales set profit=profit+'$total_profit' where id='$sale_id'";
                        $this->db->sql($update_sale_product_profit);
                        
                        //update sold quantity in purchase
                        $pid=$purchase_row['id'];
                        $update_purchase_quantity="update purchase set sold_quantity=sold_quantity+'$sale_quantity' where id='$pid'";

                        $this->db->sql($update_purchase_quantity);
                        
                        return "done";
                        
                    }else{
                        $stock_difference=$sale_quantity-$available_stock;
                        $purchase_price=$available_stock*$purchase_row['rate_adjustment'];
                        $sales_price=$available_stock*$sales[0]['rate'];
                        $profit_diff=$sales_price-$purchase_price;
                        $total_profit+=$profit_diff;
                        
                        //update sold quantity in purchase
                        $pid=$purchase_row['id'];
                        $update_purchase_quantity_else="update purchase set sold_quantity=sold_quantity+'$available_stock' where id='$pid'";
                        $this->db->sql($update_purchase_quantity_else);
                    
                        $sale_quantity=$stock_difference;
                        
                    }
                }
            }//loop
        }
    }
    function calculateFreight($salesid,$frieght){
        $update_sale_product_profit="update sales set profit=profit-'$frieght' where id='$salesid'";
        if($this->db->sql($update_sale_product_profit)){
            return true;
        }else{
            return false;
        }
    }

    function calculateChangeRateProfit($pid,$openingStock,$current_sale_value,$current_purchase){
        $sale_quantity=$openingStock;
        $purchase_query="select * from purchase where product_id='$pid' and CAST(sold_quantity AS UNSIGNED) < CAST(stock AS UNSIGNED) order by purchase_date asc";
        if($this->db->sql($purchase_query)){
            $purchase_result=$this->db->getResult();

            $profit_arr=[];
            $total_profit=0;
            foreach ($purchase_result as $purchase_row){
                $available_stock=$purchase_row['stock']-$purchase_row['sold_quantity'];
                if($purchase_row['stock'] > $purchase_row['sold_quantity']){
                    if($sale_quantity<=$available_stock){
                        $purchase_price_difference = $current_purchase - $purchase_row['rate'];
                        $profit_price=$sale_quantity*$purchase_price_difference;
//                        $purchase_price=$sale_quantity*$purchase_row['rate_adjustment'];
//                        $sale_price=$current_sale_value*$sale_quantity;

//                        $profit_price=$sale_price-$purchase_price;
                        $total_profit+=$profit_price;
                        return $total_profit;

                    }else{

                        $purchase_price_difference = $current_purchase - $purchase_row['rate'];
                        $profit_price=$available_stock*$purchase_price_difference;
                        $total_profit+=$profit_price;

                        $stock_difference=$sale_quantity-$available_stock;
                        $sale_quantity=$stock_difference;
//                        return $total_profit;
                    }
                }
            }//loop
        }
    }
    function checkProductStockExist($pid,$quantity){
        $product_stock_in_tanks=$this->getProductStockInTanks($pid);
        $stock_in_tank=$product_stock_in_tanks[0]['product_stock'];
        if(empty($product_stock_in_tanks[0]['product_stock'])){
            return 0;
        }else if((float)$product_stock_in_tanks[0]['product_stock']>=$quantity){
            return $stock_in_tank;
        }else{
            return 0;
        }
    }

    function getProfitSheetInfo($start_date,$end_date,$product_id=""){
        $current_date=date('Y-m-d');

        $query='where 1=1';
        if($product_id!=''){
            $query.=" and product_id='$product_id'";
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(create_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(create_date) BETWEEN '$current_date' AND '$current_date'";
        }

        $sql="SELECT product_id,SUM(profit) as profit,sum(quantity) as soldstock FROM `sales` ".$query." GROUP by product_id ASC";
        // echo $sql;
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getprofitlossByVendor($start_date,$end_date, $vendorid="",$vendortype="",$product_id=""){
        

        $query='where 1=1';
        if($product_id!=''){
            $query.=" and product_id='$product_id'";
        }

        if($vendorid!='' && $vendortype!=''){
            $query.=" and s.customer_id='$vendorid' and s.vendor_type = '$vendortype'";
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(s.create_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $start_date = date('Y-m-01');
            $end_date=date('Y-m-d');
            $query.=" and DATE(s.create_date) BETWEEN '$start_date' AND '$end_date'";
        }

        $sql="SELECT s.customer_id, s.product_id, SUM(s.profit) as profit, sum(s.quantity) as soldstock, p.name as product_name FROM `sales` s join products p on s.product_id = p.id ".$query."  GROUP by s.product_id ASC;";
        // echo $sql;
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }


    function getAllExpenses(){
        $sql="select * from expenses";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getSingleExpenses($eid){
        $sql="select * from expenses where eid='$eid'";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }


    function getAllIncomes(){
        $sql="select * from incomes";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getSingleIncome($id){
        $sql="select * from incomes where id='$id'";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getAllExpenseTransactions($expense_type,$start_date,$end_date){
        $current_date=date('Y-m-d');
        $query='where 1=1';
        if($expense_type!=''){
            $query.=" and expense_id='$expense_type'";
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(expense_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(expense_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="select * from expense_transactions ".$query." order by expense_trans_id ASC";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getgainfrompurchase($start_date="",$end_date =""){
        $current_date=date('Y-m-d');
        $query='where 1=1';


        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(p.purchase_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(p.purchase_date) BETWEEN '$current_date' AND '$current_date'";
        }
//        $sql = "select id,supplier_id,SUM(total_amount) as amount from purchase ".$query."
//        and (vendor_type='4' or vendor_type ='5')GROUP by supplier_id,vendor_type order by id;";

        $sql = "select SUM(p.total_amount) as amount, pro.name as product_name,p.stock as quantity from 
            purchase p join products pro on p.product_id = pro.id  ".$query."
        and (p.vendor_type='4' or p.vendor_type ='5')GROUP by p.supplier_id,p.vendor_type,p.product_id order by p.id;";

        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }
    function getlossfromsale($start_date="",$end_date =""){
        $current_date=date('Y-m-d');
        $query='where 1=1';
        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(s.create_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(s.create_date) BETWEEN '$current_date' AND '$current_date'";
        }
//        $sql = "select id,customer_id,SUM(amount) as amount from sales ".$query."
//        and (vendor_type='4' or vendor_type ='5')GROUP by customer_id,vendor_type order by id;";
        $sql ="select SUM(s.amount) as amount, s.quantity as quantity, p.name as product_name from sales s join products p on s.product_id = p.id
        ".$query." and (s.vendor_type='4' or s.vendor_type ='5')GROUP by s.customer_id,s.vendor_type,s.product_id order by s.id;";

        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }



    // new functions for getting expense from transactions table
    function getAllExpenseTransactions_new_from_transactions_for_profitsheet($expense_type,$start_date,$end_date){
        $current_date=date('Y-m-d');
        $query='where 1=1';
        if($expense_type!=''){
            $query.=" and vendor_id='$expense_type'";
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        // $sql="select * from transactions ".$query." and vendor_type='4' order by tid ASC";

//         $sql="select tid,vendor_name,sum(amount) as amount from transactions ".$query." and vendor_type='4' GROUP by vendor_id,vendor_name order by tid ";
        $sql = "select tid,vendor_name,SUM(CASE WHEN transaction_type = 2 THEN amount ELSE -amount END) as amount from transactions ".$query." and vendor_type='4' GROUP by vendor_id,vendor_name order by tid;";

        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    // new functions for getting expense from transactions table
    function getAllExpenseTransactions_new_from_transactions($expense_type,$start_date,$end_date){
        $current_date=date('Y-m-d');
        $query='where 1=1';
        if($expense_type!=''){
            $query.=" and vendor_id='$expense_type'";
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="select * from transactions ".$query." and vendor_type='4' order by tid ASC";

         // $sql="select tid,vendor_name,sum(amount) as amount from transactions ".$query." and vendor_type='4' GROUP by vendor_id,vendor_name order by tid ";

        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }


    function getAllIncomeTransactions_new_from_transactions_for_profitsheet($income_type,$start_date,$end_date){
        $current_date=date('Y-m-d');
        $query='where 1=1';
        if($income_type!=''){
            $query.=" and vendor_id='$income_type'";
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        // $sql="select * from transactions ".$query." and vendor_type='5' order by tid ASC";
        $sql="select tid,vendor_name,SUM(CASE WHEN transaction_type = 2 THEN -amount ELSE amount END) as amount from transactions ".$query." and vendor_type='5' GROUP by vendor_id,vendor_name order by tid ";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getAllIncomeTransactions_new_from_transactions($income_type,$start_date,$end_date){
        $current_date=date('Y-m-d');
        $query='where 1=1';
        if($income_type!=''){
            $query.=" and vendor_id='$income_type'";
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="select * from transactions ".$query." and vendor_type='5' order by tid ASC";
        // $sql="select tid,vendor_name,sum(amount) as amount from transactions ".$query." and vendor_type='5' GROUP by vendor_id,vendor_name order by tid ";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }


    function getAllIncomeTransactions($income_type,$start_date,$end_date){
        $current_date=date('Y-m-d');
        $query='where 1=1';
        if($income_type!=''){
            $query.=" and income_id='$income_type'";
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(income_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(income_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="select * from income_transactions ".$query." order by income_tarns_id ASC";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }


    function getDipsMM($tank_id,$mm){
        $sql="SELECT * FROM `dip_charts` where tank_id='$tank_id' and mm='$mm'";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }
    function getAllDips($tank_id,$start_date,$end_date){
        $query=" where 1=1";
        $current_start_date=date("Y-m-01");
        $current_end_date=date("Y-m-d");
        if($tank_id!='' && ($tank_id!=0 || $tank_id!='0')){
            $query.=" and tankId='$tank_id'";
        }else if($tank_id=='0' || $tank_id==0){

        }
        else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(dip_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(dip_date) BETWEEN '$current_start_date' AND '$current_end_date'";
        }

        $sql="SELECT * FROM `dips` ".$query." order by date(dip_date) asc";
//        echo $sql;
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getSingleDip($dip_id){
        $sql="SELECT * FROM `dips` where id='$dip_id'";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getAllVendorLedgerInfo($vendor_type,$start_date,$end_date){
        $current_date=date('Y-m-d');
        $query="Where 1=1";
        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }

         if($vendor_type!=''){
             $query.=" and vendor_type='$vendor_type'";
         }
        $sql="SELECT * from `ledger` ".$query;

        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function supplierLedgerForTrailBalance($supplier_id,$start_date,$end_date){
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($supplier_id!=''){
            $query.=" and vendor_type='1' and vendor_id='$supplier_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            foreach ($data as $entry){
                //debit
                if($entry['transaction_type']==2 || $entry['transaction_type']=='2'){
                    $final_balance-=$entry['amount'];
                    $debit_sum+=$entry['amount'];
                    
                }
                //credit
                if($entry['transaction_type']==1 || $entry['transaction_type']=='1'){
                     $final_balance+=$entry['amount'];
                     $credit_sum+=$entry['amount'];
                }
            }
            $single_supplier_info=$this->getSingleSupplier($supplier_id);

            $object=new stdClass();
            $object->debit=$debit_sum;
            $object->v_id=$supplier_id;
            $object->credit=$credit_sum;
            $object->final_balance=$final_balance;
            $object->account_name=$single_supplier_info[0]['name'];
            $object->type='Supplier';
            return json_encode($object);
        }
    }
    function getsupplierbalance($supplier_id,$start_date,$end_date){
        if($start_date==""){
            $start_date = "1970-01-01";
        }
        if($end_date==""){
            $end_date = date('Y-m-d');
        }
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($supplier_id!=''){
            $query.=" and vendor_type='1' and vendor_id='$supplier_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            foreach ($data as $entry){
                //debit
                if($entry['transaction_type']==2 || $entry['transaction_type']=='2'){
                    $final_balance-=$entry['amount'];
                    $debit_sum+=$entry['amount'];

                }
                //credit
                if($entry['transaction_type']==1 || $entry['transaction_type']=='1'){
                    $final_balance+=$entry['amount'];
                    $credit_sum+=$entry['amount'];
                }
            }
//            $single_supplier_info=$this->getSingleSupplier($supplier_id);

            $object=new stdClass();
            $object->debit=$debit_sum;
            $object->credit=$credit_sum;
            $object->final_balance=$final_balance;
//            $object->account_name=$single_supplier_info[0]['name'];
            $object->type='Supplier';
            return json_encode($object);
        }
    }

    //get vendor sales
    function getsalesbyvendor($vendor_id,$vendor_type,$start_date="",$end_date="", $product_id=""){
        $query = 'where 1=1';
        if ($vendor_id != '') {
            $query .= " and s.vendor_type='$vendor_type' and s.customer_id='$vendor_id'";
        } else {
            return [];
        }

        if ($start_date != '' && $end_date != '') {
            $query .= " and DATE(s.create_date) BETWEEN '$start_date' AND '$end_date'";
        }
        if ($product_id != '') {
            $query .= " and s.product_id='$product_id' ";
        }
        // $query .= " and p.is_dippable = 1 ";

        // $sql = "SELECT sum(s.quantity) as sales, p.name as product_name  FROM `sales` s join products p on s.product_id=p.id " . $query. " group by s.product_id";
        $sql = "SELECT sum(s.quantity) as sales  FROM `sales` s ". $query. " group by s.product_id";
        // echo $sql;


        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }
    //get vendor purchase
    function getpurchasebyvendor($vendor_id,$vendor_type,$start_date="",$end_date="",$product_id=""){
        $query = 'where 1=1';
        if ($vendor_id != '') {
            $query .= " and p.vendor_type='$vendor_type' and p.supplier_id='$vendor_id'";
        } else {
            return [];
        }

        if ($start_date != '' && $end_date != '') {
            $query .= " and DATE(p.purchase_date) BETWEEN '$start_date' AND '$end_date'";
        }
        if ($product_id != '') {
            $query .= " and p.product_id='$product_id' ";
        }
        // $query .= " and pp.is_dippable = 1 ";

        // $sql = " SELECT sum(p.stock) as purchase, pp.name as product_name FROM `purchase` p  join products pp on p.product_id = pp.id " . $query;
        $sql = " SELECT sum(p.stock) as purchase FROM `purchase` p " . $query;
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }
    function customerLedgerForTrailBalance($customer_id,$start_date,$end_date){
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($customer_id!=''){
            $query.=" and vendor_type='2' and vendor_id='$customer_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            foreach ($data as $entry){
                //debit
                if($entry['transaction_type']==2 || $entry['transaction_type']=='2'){
                     $debit_sum+=$entry['amount'];
                     $final_balance+=$entry['amount'];
                }
                //credit
                if($entry['transaction_type']==1 || $entry['transaction_type']=='1'){
                     $final_balance-=$entry['amount'];
                     $credit_sum+=$entry['amount'];
                }
            }
            $single_customer_info=$this->getSingleCustomer($customer_id);

            $object=new stdClass();
            $object->debit=$debit_sum;
            $object->v_id=$customer_id;
            $object->credit=$credit_sum;
            $object->final_balance=$final_balance;
            $object->account_name=$single_customer_info[0]['name'];
            $object->type='Customer';
            return json_encode($object);
        }
    }
    function getcustomerbalance($customer_id,$start_date="",$end_date=""){

        if($start_date==""){
            $start_date = "1970-01-01";
        }
        if($end_date==""){
            $end_date = date('Y-m-d');
        }
        $query='where 1=1';
        $current_date=date('Y-m-d');

        if($customer_id!=''){
            $query.=" and vendor_type='2' and vendor_id='$customer_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            foreach ($data as $entry){
                //debit
                if($entry['transaction_type']==2 || $entry['transaction_type']=='2'){
                    $debit_sum+=$entry['amount'];
                    $final_balance+=$entry['amount'];
                }
                //credit
                if($entry['transaction_type']==1 || $entry['transaction_type']=='1'){
                    $final_balance-=$entry['amount'];
                    $credit_sum+=$entry['amount'];
                }
            }

            $object=new stdClass();
            $object->debit=$debit_sum;
            $object->credit=$credit_sum;
            $object->final_balance=$final_balance;

            return json_encode($object);
        }
    }

    function productLedgerForTrailBalance($product_id,$start_date,$end_date){
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($product_id!=''){
            $query.=" and vendor_type='3' and vendor_id='$product_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            foreach ($data as $entry){
                //debit
                if($entry['transaction_type']==2 || $entry['transaction_type']=='2'){
                    $debit_sum+=$entry['amount'];
                     $final_balance+=$entry['amount'];
                }
                //credit
                if($entry['transaction_type']==1 || $entry['transaction_type']=='1'){
                    $credit_sum+=$entry['amount'];
                     $final_balance-=$entry['amount'];
                }
            }
            $single_product_info=$this->getSingleProduct($product_id);
            if(!empty($single_product_info)){
                $productname = $single_product_info[0]['name'];
            }else{
                $productname =  "Not found / deleted";
            }
            $product_stock=$this->getProductStockInTanks($product_id);
            if(!empty($product_stock)){
                $stock = $product_stock[0]['product_stock'];
            }else{
                $stock =  "0";
            }
            $object=new stdClass();
            $object->debit=$debit_sum;
            $object->credit=$credit_sum;
            $object->final_balance=$final_balance;
            $object->account_name=$productname;
            $object->product_stock=$stock;
            $object->type='Product';
            return json_encode($object);
        }
    }
    function getproductbalance($product_id,$start_date,$end_date){
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($product_id!=''){
            $query.=" and vendor_type='3' and vendor_id='$product_id'";
        }else{
            return [];
        }

        if($start_date==""){
            $start_date = "1970-01-01";
        }
        if($end_date==""){
            $end_date = date('Y-m-d');
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            foreach ($data as $entry){
                //debit
                if($entry['transaction_type']==2 || $entry['transaction_type']=='2'){
                    $debit_sum+=$entry['amount'];
                    $final_balance+=$entry['amount'];
                }
                //credit
                if($entry['transaction_type']==1 || $entry['transaction_type']=='1'){
                    $credit_sum+=$entry['amount'];
                    $final_balance-=$entry['amount'];
                }
            }
//            $single_product_info=$this->getSingleProduct($product_id);
            $object=new stdClass();
            $object->debit=$debit_sum;
            $object->credit=$credit_sum;
            $object->final_balance=$final_balance;
//            $object->account_name=$single_product_info[0]['name'];
            $object->type='Product';
            return json_encode($object);
        }
    }

    function expenseLedgerForTrailBalance($expense_id,$start_date,$end_date){
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($expense_id!=''){
            $query.=" and vendor_type='4' and vendor_id='$expense_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            foreach ($data as $entry){
                //debit
                if($entry['transaction_type']==2 || $entry['transaction_type']=='2'){
                    $debit_sum+=$entry['amount'];
                    $final_balance+=$entry['amount'];
                }
                //credit
                if($entry['transaction_type']==1 || $entry['transaction_type']=='1'){
                   $credit_sum+=$entry['amount'];
                    $final_balance-=$entry['amount'];
                }
            }
            $single_expense_info=$this->getSingleExpenses($expense_id);
            $object=new stdClass();
            $object->debit=$debit_sum;
            $object->credit=$credit_sum;
            $object->final_balance=$final_balance;
            $object->account_name=$single_expense_info[0]['expense_name'];
            $object->type='Expense';
            return json_encode($object);
        }
    }
    function getexpensebalance($expense_id,$start_date,$end_date){
        if($start_date==""){
            $start_date = "1970-01-01";
        }
        if($end_date==""){
            $end_date = date('Y-m-d');
        }

        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($expense_id!=''){
            $query.=" and vendor_type='4' and vendor_id='$expense_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            foreach ($data as $entry){
                //debit
                if($entry['transaction_type']==2 || $entry['transaction_type']=='2'){
                    $debit_sum+=$entry['amount'];
                    $final_balance+=$entry['amount'];
                }
                //credit
                if($entry['transaction_type']==1 || $entry['transaction_type']=='1'){
                   $credit_sum+=$entry['amount'];
                    $final_balance-=$entry['amount'];
                }
            }
//            $single_expense_info=$this->getSingleExpenses($expense_id);
            $object=new stdClass();
            $object->debit=$debit_sum;
            $object->credit=$credit_sum;
            $object->final_balance=$final_balance;
//            $object->account_name=$single_expense_info[0]['expense_name'];
            $object->type='Expense';
            return json_encode($object);
        }
    }

    function incomeLedgerForTrailBalance($income_id,$start_date,$end_date){
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($income_id!=''){
            $query.=" and vendor_type='5' and vendor_id='$income_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            foreach ($data as $entry){
                //debit
                if($entry['transaction_type']==2 || $entry['transaction_type']=='2'){
                    $debit_sum+=$entry['amount'];
                    $final_balance-=$entry['amount'];
                }
                //credit
                if($entry['transaction_type']==1 || $entry['transaction_type']=='1'){
                    $credit_sum+=$entry['amount'];
                    $final_balance+=$entry['amount'];

                }
            }
            $single_income_info=$this->getSingleIncome($income_id);
            $object=new stdClass();
            $object->debit=$debit_sum;
            $object->credit=$credit_sum;
            $object->final_balance=$final_balance;
            $object->account_name=$single_income_info[0]['income_name'];
            $object->type='Income';
            return json_encode($object);
        }
    }
    function getincomebalance($income_id,$start_date,$end_date){
        if($start_date==""){
            $start_date = "1970-01-01";
        }
        if($end_date==""){
            $end_date = date('Y-m-d');
        }
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($income_id!=''){
            $query.=" and vendor_type='5' and vendor_id='$income_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            foreach ($data as $entry){
                //debit
                if($entry['transaction_type']==2 || $entry['transaction_type']=='2'){
                    $debit_sum+=$entry['amount'];
                    $final_balance-=$entry['amount'];
                }
                //credit
                if($entry['transaction_type']==1 || $entry['transaction_type']=='1'){
                    $credit_sum+=$entry['amount'];
                    $final_balance+=$entry['amount'];

                }
            }
//            $single_income_info=$this->getSingleIncome($income_id);
            $object=new stdClass();
            $object->debit=$debit_sum;
            $object->credit=$credit_sum;
            $object->final_balance=$final_balance;
//            $object->account_name=$single_income_info[0]['income_name'];
            $object->type='Income';
            return json_encode($object);
        }
    }

    function bankLedgerForTrailBalance($bank_id,$start_date,$end_date){
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($bank_id!=''){
            $query.=" and vendor_type='6' and vendor_id='$bank_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            foreach ($data as $entry){
                //debit
                if($entry['transaction_type']==2 || $entry['transaction_type']=='2'){
                     $debit_sum+=$entry['amount'];
                     $final_balance+=$entry['amount'];
                }
                //credit
                if($entry['transaction_type']==1 || $entry['transaction_type']=='1'){
                    $final_balance-=$entry['amount'];
                    $credit_sum+=$entry['amount'];
                }
            }
            $single_bank_info=$this->getSingleBank($bank_id);
            if(empty($single_bank_info)){
                $bank_name = "";
            }else{
                $bank_name = $single_bank_info[0]['name'];
            }

            $object=new stdClass();
            $object->debit=$debit_sum;
            $object->credit=$credit_sum;
            $object->final_balance=$final_balance;
            $object->account_name=$bank_name;
            $object->type='Bank';
            return json_encode($object);
        }
    }

    function bankLedgerForEmployee($vendor_id,$start_date,$end_date){
        $query='where 1=1';
        $current_date=date('Y-m-d');
        if($vendor_id!=''){
            $query.=" and vendor_type='9' and vendor_id='$vendor_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            foreach ($data as $entry){
                //debit
                if($entry['transaction_type']==2 || $entry['transaction_type']=='2'){
                     $debit_sum+=$entry['amount'];
                     $final_balance+=$entry['amount'];
                }
                //credit
                if($entry['transaction_type']==1 || $entry['transaction_type']=='1'){
                    $final_balance-=$entry['amount'];
                    $credit_sum+=$entry['amount'];
                }
            }
            $employedetails=$this->getSingleEmployee($vendor_id,3);
            if(empty($employedetails)){
                $employename = "";
            }else{
                $employename = $employedetails[0]['name'];
            }

            $object=new stdClass();
            $object->debit=$debit_sum;
            $object->credit=$credit_sum;
            $object->final_balance=$final_balance;
            $object->account_name=$employename;
            $object->type='Employee';
            return json_encode($object);
        }
    }
    function getbankbalance($bank_id,$start_date,$end_date){
        $query='where 1=1';
        if($start_date==""){
            $start_date = "1970-01-01";
        }
        if($end_date==""){
            $end_date = date('Y-m-d');
        }

        $current_date=date('Y-m-d');
        if($bank_id!=''){
            $query.=" and vendor_type='6' and vendor_id='$bank_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            foreach ($data as $entry){
                //debit
                if($entry['transaction_type']==2 || $entry['transaction_type']=='2'){
                     $debit_sum+=$entry['amount'];
                     $final_balance+=$entry['amount'];
                }
                //credit
                if($entry['transaction_type']==1 || $entry['transaction_type']=='1'){
                    $final_balance-=$entry['amount'];
                    $credit_sum+=$entry['amount'];
                }
            }
//            $single_bank_info=$this->getSingleBank($bank_id);
            $object=new stdClass();
            $object->debit=$debit_sum;
            $object->credit=$credit_sum;
            $object->final_balance=$final_balance;
//            $object->account_name=$single_bank_info[0]['name'];
//            $object->type='Bank';
            return json_encode($object);
        }
    }

    function getEmployeebalance($emp_id,$start_date,$end_date){
        $query='where 1=1';
        if($start_date==""){
            $start_date = "1970-01-01";
        }
        if($end_date==""){
            $end_date = date('Y-m-d');
        }

        $current_date=date('Y-m-d');
        if($emp_id!=''){
            $query.=" and vendor_type='9' and vendor_id='$emp_id'";
        }else{
            return [];
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            foreach ($data as $entry){
                //debit
                if($entry['transaction_type']==2 || $entry['transaction_type']=='2'){
                     $debit_sum+=$entry['amount'];
                     $final_balance+=$entry['amount'];
                }
                //credit
                if($entry['transaction_type']==1 || $entry['transaction_type']=='1'){
                    $final_balance-=$entry['amount'];
                    $credit_sum+=$entry['amount'];
                }
            }
//            $single_bank_info=$this->getSingleBank($bank_id);
            $object=new stdClass();
            $object->debit=$debit_sum;
            $object->credit=$credit_sum;
            $object->final_balance=$final_balance;
//            $object->account_name=$single_bank_info[0]['name'];
//            $object->type='Bank';
            return json_encode($object);
        }
    }

    function cashLedgerForTrailBalance($start_date,$end_date){
        $query="where 1=1 and vendor_type='7'";
        $current_date=date('Y-m-d');

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            if(!empty($data)) {
                foreach ($data as $entry) {
                    //debit
                    if ($entry['transaction_type'] == 2 || $entry['transaction_type'] == '2') {
                        $debit_sum += $entry['amount'];
                        $final_balance += $entry['amount'];
                    }
                    //credit
                    if ($entry['transaction_type'] == 1 || $entry['transaction_type'] == '1') {
                        $final_balance -= $entry['amount'];
                        $credit_sum += $entry['amount'];
                    }
                }
                $object = new stdClass();
                $object->debit = $debit_sum;
                $object->credit = $credit_sum;
                $object->final_balance = $final_balance;
                $object->account_name = "Cash";
                $object->type = 'Cash';
                return json_encode($object);
            }else{
                return [];
            }
        }
    }
    function getcashbalance($start_date,$end_date){
        if($start_date==""){
            $start_date = "1970-01-01";
        }
        if($end_date==""){
            $end_date = date('Y-m-d');
        }
        $query="where 1=1 and vendor_type='7'";

        $current_date=date('Y-m-d');

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            if(!empty($data)) {
                foreach ($data as $entry) {
                    //debit
                    if ($entry['transaction_type'] == 2 || $entry['transaction_type'] == '2') {
                        $debit_sum += $entry['amount'];
                        $final_balance += $entry['amount'];
                    }
                    //credit
                    if ($entry['transaction_type'] == 1 || $entry['transaction_type'] == '1') {
                        $final_balance -= $entry['amount'];
                        $credit_sum += $entry['amount'];
                    }
                }
                $object = new stdClass();
                $object->debit = $debit_sum;
                $object->credit = $credit_sum;
                $object->final_balance = $final_balance;
                $object->account_name = "Cash";
                $object->type = 'Cash';
                return json_encode($object);
            }else{
                $object=new stdClass();
                $object->debit=0;
                $object->credit=0;
                $object->final_balance=0;
                return json_encode($object);
            }
        }
    }

    function mpLedgerForTrailBalance($start_date,$end_date){
        $query="where 1=1 and vendor_type='8'";
        $current_date=date('Y-m-d');

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            if(!empty($data)) {
                foreach ($data as $entry) {
                    //debit
                    if ($entry['transaction_type'] == 2 || $entry['transaction_type'] == '2') {
                        $debit_sum += $entry['amount'];
                        $final_balance += $entry['amount'];
                    }
                    //credit
                    if ($entry['transaction_type'] == 1 || $entry['transaction_type'] == '1') {
                        $final_balance -= $entry['amount'];
                        $credit_sum += $entry['amount'];
                    }
                }
                $object = new stdClass();
                $object->debit = $debit_sum;
                $object->credit = $credit_sum;
                $object->final_balance = $final_balance;
                $object->account_name = "MP";
                $object->type = 'MP';
                return json_encode($object);
            }else{
                return [];
            }
        }
    }
    function getmpbalance($start_date,$end_date){
        if($start_date==""){
            $start_date = "1970-01-01";
        }
        if($end_date==""){
            $end_date = date('Y-m-d');
        }
        $query="where 1=1 and vendor_type='8'";
        $current_date=date('Y-m-d');

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transaction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transaction_date) BETWEEN '$current_date' AND '$current_date'";
        }
        $sql="SELECT * FROM `ledger` ".$query;
        if($this->db->sql($sql)){
            $data= $this->db->getResult();
            $debit_sum=0;
            $credit_sum=0;
            $final_balance=0;
            if(!empty($data)) {
                foreach ($data as $entry) {
                    //debit
                    if ($entry['transaction_type'] == 2 || $entry['transaction_type'] == '2') {
                        $debit_sum += $entry['amount'];
                        $final_balance += $entry['amount'];
                    }
                    //credit
                    if ($entry['transaction_type'] == 1 || $entry['transaction_type'] == '1') {
                        $final_balance -= $entry['amount'];
                        $credit_sum += $entry['amount'];
                    }
                }
                $object = new stdClass();
                $object->debit = $debit_sum;
                $object->credit = $credit_sum;
                $object->final_balance = $final_balance;
                $object->account_name = "MP";
                $object->type = 'MP';
                return json_encode($object);
            }else{
                $object = new stdClass();
                $object->debit = 0;
                $object->credit = 0;
                $object->final_balance = 0;
                $object->account_name = "MP";
                $object->type = 'MP';
                return json_encode($object);
            }
        }
    }


    function getAllSearchDips($tank_id,$start_date,$end_date){

        $sql="select * from tanks";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }

    function getPurchaseByTankId($tankId,$dip_date){
        $sql="select SUM(stock) as purchase_stock from purchase where tank_id='$tankId' and DATE(purchase_date)='$dip_date'";
        if($this->db->sql($sql)){
            return $this->db->getResult();
        }
    }
    function getpreviousdipstock($tankid){
        $sql = "SELECT * FROM `dips` where tankId = '$tankid' order by id desc limit 1;";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }

    function getSaleByTankId($tankId,$dip_date)
    {
        $sql = "select SUM(quantity) as sale_stock from sales where tank_id='$tankId' and DATE(create_date)='$dip_date'";
        
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }

    function getProductStockInTanks($product_id)
    {
        $sql = "select SUM(opening_stock) as product_stock from tanks where product_id='$product_id'";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }


    function insertLedgerQuery($tid,$tank_id,$product_id,$purchase_type,$vendor_type,$vendor_id,$transaction_type,$amount,$previous,$comment,$transaction_date=""){
        if($transaction_date==""){
            $transaction_date=date("Y-m-d");
        }
        $sql="INSERT INTO `ledger`(`transaction_id`, `tank_id`, `product_id`, `purchase_type`, `vendor_type`, `vendor_id`, `transaction_type`, `amount`, `previous_balance`, `tarnsaction_comment`,`transaction_date`)
              VALUES ('$tid','$tank_id','$product_id','$purchase_type','$vendor_type','$vendor_id','$transaction_type','$amount','$previous','$comment','$transaction_date')";
              // echo $sql;
        if($this->db->sql($sql)){
            return true;
        }else{
            return false;
        }
    }
    function getlastidofsales(){

        $sql = "SELECT MAX(id) AS last_row_id, product_id FROM sales GROUP BY product_id;";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }
    function getsitesettings(){
        $sql = "SELECT * FROM settings limit 1";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }

    }

    function reverseStockAferSaleDelete($saleid){
        $saledetails = $this->getSingleSales($saleid);
        $tank_id = $saledetails[0]['tank_id'];
        $product_id = $saledetails[0]['product_id'];
        $stocksold = (float) $saledetails[0]['quantity'];

        $update_tank_table="UPDATE `tanks` SET `opening_stock`=opening_stock+'$stocksold' WHERE id='$tank_id'";
        $this->db->sql($update_tank_table);


        //reversing sold stocks in purchases
        $sql = "SELECT * FROM `purchase` where product_id = '$product_id' and (sold_quantity!=0 or sold_quantity!=0.00) ORDER BY `id` DESC;";
        if($this->db->sql($sql)){
            $purchase_result=$this->db->getResult();

            foreach ($purchase_result as $purchase_row){
                    $purchasedstock=$purchase_row['sold_quantity'];

                    if($purchasedstock>=$stocksold){
                        //update sold quantity in purchase
                        $pid=$purchase_row['id'];
                        $update_purchase_quantity="update purchase set sold_quantity=sold_quantity-'$stocksold' where id='$pid'";
                        $this->db->sql($update_purchase_quantity);
                        return "done";

                    }else{
                        $stock_difference = $stocksold-$purchasedstock;
                        //update sold quantity in purchase
                        $pid=$purchase_row['id'];
                        $update_purchase_quantity_else="update purchase set sold_quantity=0 where id='$pid'";
                        $this->db->sql($update_purchase_quantity_else);
                        $stocksold=$stock_difference;
                    }

            }//loop
        }

    }

    function getcurrentcash(){
        $start_date = "1970-01-01";
        $end_date = date("Y-m-d");
        $ledgers=$this->getCashLedger($start_date,$end_date);
        $debit_sum = 0;
        $final_balance = 0;
        $credit_sum = 0;
        foreach ($ledgers as $ledger){
                    if($ledger['transaction_type']==2 || $ledger['transaction_type']=='2'){
                        $debit_sum+=$ledger['amount'];
                        $final_balance+=$ledger['amount'];
                    }
                    if($ledger['transaction_type']==1 || $ledger['transaction_type']=='1'){
                        $final_balance-=$ledger['amount'];
                        $credit_sum+=$ledger['amount'];
                    }
        }
//        $obj = new stdClass();
//        $obj->finalbalance = $final_balance;
//        return $obj;
        return $final_balance;
    }
    function getproductssales($startdate = "", $enddate = "", $vendor_id= "", $vendor_type=""){
        $query='where 1=1';
        if($startdate!=''){
            $query.=" and s.create_date >= '$startdate' ";
        }

        if($enddate!=''){
            $query.=" and s.create_date <= '$enddate' ";
        }

        if($vendor_id!='' && $vendor_type != ""){
            $query.=" and s.customer_id = '$vendor_id' and s.vendor_type = '$vendor_type' ";
        }

        $sql ="SELECT s.product_id, p.name as product_name, SUM(s.quantity) AS total_quantity, SUM(s.amount) AS total_amount FROM sales s join products p on s.product_id = p.id ".$query." GROUP BY s.product_id";
//        echo $sql;
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }

    }
    function getproductssalesby_id($startdate = "", $enddate = "",$pid){


        $query='where 1=1';
        if($startdate!=''){
            $query.=" and s.create_date >= '$startdate' ";
        }

        if($enddate!=''){
            $query.=" and s.create_date <= '$enddate' ";
        }
        if($pid!=''){
            $query.=" and p.id = '$pid' ";
        }

        $sql ="SELECT s.product_id, p.name as product_name, SUM(s.quantity) AS total_quantity, SUM(s.amount) AS total_amount FROM sales s join products p on s.product_id = p.id ".$query." GROUP BY s.product_id";
//        echo $sql;
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }

    }
    //nozzle items only
    function last7dayssales(){
        $sql ="SELECT
                    n.product_id,
                    p.name AS product_name,
                    SUM(s.quantity) AS total_quantity,
                    SUM(s.amount) AS total_amount
                FROM
                    nozzle n
                JOIN
                    products p ON n.product_id = p.id
                JOIN
                    sales s ON n.product_id = s.product_id
                WHERE
                    s.create_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                GROUP BY
                    n.product_id;
                ";

        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }

    }

    //general items last 7 days
    function last7dayssales_of_general_items(){
        $sql ="SELECT
                    s.product_id,
                    p.name AS product_name,
                    SUM(s.quantity) AS total_quantity,
                    SUM(s.amount) AS total_amount
                FROM
                    sales s
                JOIN
                    products p ON s.product_id = p.id
                LEFT JOIN
                    nozzle n ON s.product_id = n.product_id
                WHERE
                    s.create_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                    AND n.product_id IS NULL
                GROUP BY
                    s.product_id;
                ";

        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }

    }


    function getproductpurchase($startdate = "", $enddate = "", $vendor_id = "", $vendor_type = ""){
        $query=' where 1=1';
        if($startdate!=''){
            $query.=" and s.purchase_date >= '$startdate' ";
        }

        if($enddate!=''){
            $query.=" and s.purchase_date <= '$enddate' ";
        }
        if($vendor_id!='' && $vendor_type != ""){
            $query.=" and s.supplier_id = '$vendor_id' and s.vendor_type = '$vendor_type' ";
        }

        $sql = "SELECT s.product_id, p.name as product_name, SUM(s.stock) AS total_quantity, SUM(s.total_amount) AS total_amount FROM purchase s join products p on s.product_id = p.id".$query." GROUP BY s.product_id";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }
    function getproductpurchaseby_pid($startdate = "", $enddate = "",$product_id){
        $query=' where 1=1';
        if($startdate!=''){
            $query.=" and s.purchase_date >= '$startdate' ";
        }

        if($enddate!=''){
            $query.=" and s.purchase_date <= '$enddate' ";
        }

        if($product_id!=''){
            $query.=" and p.id = '$product_id' ";
        }

        $sql = "SELECT s.product_id, p.name as product_name, SUM(s.stock) AS total_quantity, SUM(s.total_amount) AS total_amount FROM purchase s join products p on s.product_id = p.id".$query." GROUP BY s.product_id";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }

    }
    function getbanknamefromtransaction($transactionid){
        $sql = "SELECT * FROM `transactions` where tid = '$transactionid';";
        if ($this->db->sql($sql)) {
            $details = $this->db->getResult();
            if(isset($details[0]['bank_name'])){
                return $details[0]['bank_name'];    
            }else{
                return "";
            }
            
        }
    }

    function getvendornamefromtransaction($transactionid){
        $sql = "SELECT * FROM `transactions` where tid = '$transactionid';";
        if ($this->db->sql($sql)) {
            $details = $this->db->getResult();
            return $details[0]['vendor_name'];
        }
    }

    function getincomenamefromtransasction($transactionid){
        $sql = "SELECT * FROM `income_transactions` where income_tarns_id = '$transactionid';";
        if ($this->db->sql($sql)) {
            $details = $this->db->getResult();
            return $details[0]['income_type_name'];
        }
    }
    function getexpensenamefromtransasction($transactionid){
        $sql = "SELECT * FROM `expense_transactions` where expense_trans_id = '$transactionid';";
        if ($this->db->sql($sql)) {
            $details = $this->db->getResult();
            return $details[0]['expense_type_name'];
        }
    }
    function getledgertransactiondetail($transactionid = "",$purchasetype = "", $vendorytype="", $vendorid =""){
        $note_rate = "";


//        purchase types
//        1=purchase,2=sale,3=bank_payment,5=income,6=expense,7=bank_receiving,8=cash_receiving,9=cash_payment,10=journal,11=mp
        //why transaction happen
        if($purchasetype=='1'){
            $note_rate .="purchase";
        }else if($purchasetype=='2'){
            $note_rate .="sale";
        }else if($purchasetype=='3'){
            $note_rate .="bank payment";
            $note_rate .=", ".$this->getvendornamefromtransaction($transactionid);
        }else if($purchasetype=='5'){
            $note_rate .="income";
            $note_rate .=", ".$this->getincomenamefromtransasction($transactionid);
        }else if($purchasetype=='6'){
            $note_rate .="expense";
            $note_rate .=", ".$this->getexpensenamefromtransasction($transactionid);
        }else if($purchasetype=='7'){
            $note_rate .="bank receiving";
            $note_rate .=", ".$this->getvendornamefromtransaction($transactionid);
        }else if($purchasetype=='8'){
            $note_rate .="cash receiving";
            $note_rate .=", ".$this->getvendornamefromtransaction($transactionid);
        }else if($purchasetype=='9'){
            $note_rate .="cash payment";
            $note_rate .=", ". $this->getvendornamefromtransaction($transactionid);
        }else if($purchasetype=='10'){
            $note_rate .="JV";
        }else if($purchasetype=='11'){
            $note_rate .="MP";
        }

        //if purchase sale get details and rate/stock
        if($purchasetype=='1'){
            //purchase
            $transactions_details=$this->getSinglePurchase($transactionid);
            $product_rate=$transactions_details[0]['rate'];
            $product_quantity=$transactions_details[0]['stock'];

            $vendor = $this->getvendorbytype($transactions_details[0]['vendor_type'],$transactions_details[0]['supplier_id']);
            $vendorname = $vendor->vendor_name;

            $product_details = $this->getSingleProduct($transactions_details[0]['product_id']);
            $product_name = $product_details[0]['name'];

            $note_rate .= ", ".$vendorname. ", ".$product_name.' '.$product_quantity ."@".$product_rate;
        }else if($purchasetype=='2'){
            //sales
            $transactions_details=$this->getSingleSales($transactionid);
            $product_rate=$transactions_details[0]['rate'];
            $product_quantity=$transactions_details[0]['quantity'];

            $vendor = $this->getvendorbytype($transactions_details[0]['vendor_type'],$transactions_details[0]['customer_id']);
            $vendorname = $vendor->vendor_name;

            $product_details = $this->getSingleProduct($transactions_details[0]['product_id']);
            $product_name = $product_details[0]['name'];
            $note_rate .= ", ".$vendorname. ", ".$product_name.' '.$product_quantity ."@".$product_rate;
        }


        return $note_rate;
    }
    function getledgertransactiondetailforparty($transactionid = "",$purchasetype = "", $vendorytype="", $vendorid =""){
        $note_rate = "";


//        purchase types
//        1=purchase,2=sale,3=bank_payment,5=income,6=expense,7=bank_receiving,8=cash_receiving,9=cash_payment,10=journal,11=mp
        //why transaction happen
        if($purchasetype=='1'){
            $note_rate .="purchase";
        }else if($purchasetype=='2'){
            $note_rate .="sale";
        }else if($purchasetype=='3'){
            $note_rate .="bank payment";
            $note_rate .=", ".$this->getbanknamefromtransaction($transactionid);
        }else if($purchasetype=='5'){
            $note_rate .="income";
            $note_rate .=", ".$this->getincomenamefromtransasction($transactionid);
        }else if($purchasetype=='6'){
            $note_rate .="expense";
            $note_rate .=", ".$this->getexpensenamefromtransasction($transactionid);
        }else if($purchasetype=='7'){
            $note_rate .="bank receiving";
            $note_rate .=", ".$this->getbanknamefromtransaction($transactionid);
        }else if($purchasetype=='8'){
            $note_rate .="cash receiving";
            $note_rate .=", ".$this->getbanknamefromtransaction($transactionid);

        }else if($purchasetype=='9'){
            $note_rate .="cash payment";
            $note_rate .=", ". $this->getbanknamefromtransaction($transactionid);
        }else if($purchasetype=='10'){
            $note_rate .="JV";
        }else if($purchasetype=='11'){
            $note_rate .="MP";
        }else if($purchasetype=='12'){ //12 = credit sales in post function 73
            $note_rate .="credit sales";
        }

        //if purchase sale get details and rate/stock
        if($purchasetype=='1'){
            //purchase
            $transactions_details=$this->getSinglePurchase($transactionid);
            $product_rate=$transactions_details[0]['rate'];
            $product_quantity=$transactions_details[0]['stock'];
            $lorry_id=$transactions_details[0]['vehicle_no'];
            $lorry_details = $this->getSingleTankLari($lorry_id);
            if(!empty($lorry_details)){
                $lorry_name = $lorry_details[0]['larry_name'];
            }else{
                $lorry_name = "";
            }


//            $vendor = $this->getvendorbytype($transactions_details[0]['vendor_type'],$transactions_details[0]['supplier_id']);
//            $vendorname = $vendor->vendor_name;

            $product_details = $this->getSingleProduct($transactions_details[0]['product_id']);
            $product_name = $product_details[0]['name'];

            $note_rate .= ", ".$product_name.' '.$product_quantity ."@".$product_rate.", ".$lorry_name;

        }else if($purchasetype=='2'){
            //sales
            $transactions_details=$this->getSingleSales($transactionid);
            $product_rate=$transactions_details[0]['rate'];
            $product_quantity=$transactions_details[0]['quantity'];

            $lorry_id=$transactions_details[0]['tank_lari_id'];
            $lorry_details = $this->getSingleTankLari($lorry_id);
            if(!empty($lorry_details)){
                $lorry_name = $lorry_details[0]['larry_name'];
            }else{
                $lorry_name = "";
            }
//            $vendor = $this->getvendorbytype($transactions_details[0]['vendor_type'],$transactions_details[0]['customer_id']);
//            $vendorname = $vendor->vendor_name;

            $product_details = $this->getSingleProduct($transactions_details[0]['product_id']);
            $product_name = $product_details[0]['name'];
            $note_rate .= ", ".$product_name.' '.$product_quantity ."@".$product_rate.", ".$lorry_name;

        }else if($purchasetype == '12'){//credit sales details
            //credit sales
            $transactions_details=$this->getCreditSalesSingle($transactionid);
            $product_rate=$transactions_details[0]['rate'];
            $product_quantity=$transactions_details[0]['quantity'];

            $lorry_id=$transactions_details[0]['vehicle_id'];
            $lorry_details = $this->getSingleTankLari($lorry_id);
            if(!empty($lorry_details)){
                $lorry_name = $lorry_details[0]['larry_name'];
            }else{
                $lorry_name = "";
            }


            $product_details = $this->getSingleProduct($transactions_details[0]['product_id']);
            $product_name = $product_details[0]['name'];
            $note_rate .= ", ".$product_name.' '.$product_quantity ."@".$product_rate.", ".$lorry_name;
            
        }
        return $note_rate;
    }
    function getAllNozzles() {
        $sql = "select * from nozzle";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }
    function getAllNozzlesbyproductid($product_id) {
        $sql = "select * from nozzle where product_id = '$product_id'";
//        echo $sql;
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }
    function getSingleNozzle($nozzleid){
        $sql = "select * from nozzle where id = '$nozzleid'";
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }
    function getCreditSales($vendor_id = "", $start_date="", $end_date="",$vendor_type=""){

        $query='where 1=1';
        $current_date=date('Y-m-d');

        if($vendor_id!='' && $vendor_type!=''){
            $query.=" and vendor_id='$vendor_id'and vendor_type='$vendor_type'";
        }

        if($start_date!='' && $end_date!=''){
            $query.=" and DATE(transasction_date) BETWEEN '$start_date' AND '$end_date'";
        }else{
            $query.=" and DATE(transasction_date) BETWEEN '$current_date' AND '$current_date'";
        }

        $sql = "select * from credit_sales ".$query;
//        echo $sql;
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }

    function getCreditSalesSingle($transactionid){

        

        $sql = "select * from credit_sales where id = ".$transactionid;
       
        if ($this->db->sql($sql)) {
            return $this->db->getResult();
        }
    }

    function checklastdipexist($productid, $dip_date){
        //check if its first sale
        $sql = "select * from sales where product_id = '$productid' limit 1";
        $this->db->sql($sql);
        $res = $this->db->numRows();
        if ($res == 0) {
            return true;
            exit();
        }

        $date = new DateTime($dip_date);
        $date->modify('-1 day');
        $last_date = $date->format('Y-m-d');

        $sql = "select * from dips where dip_date = '$last_date' and productId = '$productid'";
//        echo $sql;
        $this->db->sql($sql);
        $res = $this->db->numRows();
        if ($res > 0) {
            return true;
        }else{
            return false;
        }



    }
    function checksalesexits($tank_id, $dip_date){
        //check if its first sale
        $sql = "select * from sales where tank_id = '$tank_id' and create_date = '$dip_date'";
//        echo $sql;
        $this->db->sql($sql);
        $res = $this->db->numRows();
        if ($res > 0) {
            return true;
        } else {
            return false;
        }

    }
    function updateStockStatus($product_id, $stock_date){
        //getting previous stock
        $product_stock_in_tanks=$this->getProductStockInTanks($product_id);
        $product_previous_stock=0;
        if(empty($product_stock_in_tanks[0]['product_stock'])){
            $product_previous_stock=0;
        }else{
            $product_previous_stock=$product_stock_in_tanks[0]['product_stock'];
        }

        $sql = "select * from current_stock where product_id = '$product_id' and stock_date = '$stock_date'";
        $this->db->sql($sql);
        $res = $this->db->numRows();

        $check = 0;
        if ($res > 0) {
            $check = 1;
        } 

        if($check == 1){
            // update
            $sql = "UPDATE `current_stock` SET `stock`='$product_previous_stock' WHERE stock_date = '$stock_date' and product_id = '$product_id'";
            $this->db->sql($sql);
        }else{
            // insert
            $sql = "INSERT INTO `current_stock`(`product_id`, `stock`, `stock_date`) VALUES ('$product_id','$product_previous_stock','$stock_date')";
            $this->db->sql($sql);
        }


    }

    function getstock($product_id, $stock_date=""){
        $where = " where 1=1 ";
        
        $where .= " and product_id = '$product_id'";

        if($stock_date!=''){
            $where.=" and stock_date = '$stock_date'";
        }

        $sql = "select * from current_stock".$where;
        // echo $sql;
        if ($this->db->sql($sql)) {
            // print_r($this->db->getResult());
            return $this->db->getResult();
        }
    }
    function getlastopeningstock($product_id,$stock_date){
        $sql = "SELECT * FROM `current_stock` where stock_date < '$stock_date' and product_id = '$product_id' order by id desc limit 1;";
        if ($this->db->sql($sql)) {
            // print_r($this->db->getResult());
            return $this->db->getResult();
        }
    }
    function check_user_type(){
        $user = $_SESSION['user_type'];
        if($user==1){//1=admin
            return true;
        }else{
            return false;
        }
    }



}//class
