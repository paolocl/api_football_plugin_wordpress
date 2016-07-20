<?php

/**
 * Created by PhpStorm.
 * User: paolo
 * Date: 18/07/16
 * Time: 11:32
 */

use Model\apiFootModel;
use Model\dataModel;

class apiFoot_admin
{

    private $ip = '127.0.0.1';

    public function __construct()
    {
        $this->run();
    }

    /**
     * CALL FUNCTION TO ADD IN CONSTRUCT
     * url :
     *      %url%?updateCompetition -> update competition
     *      %url%?updateStanding -> update standing (classement)
     *      %url%?updateTeam -> update team
     *      %url%?updateMatch -> update matchs from all competition
     *      %url%?cleanDB -> clean Data base
     **/
    private function run()
    {
        /**
         * creation of necessary table
         */
        $dataModel = new \Model\dataModel\dataModel();
        $dataModel->createOptionApiTable();
        $dataModel->createCompetitionTable();

        /**
         * get the token with is set at the creation of option api table it's use to secure the update
         */
        $token = $dataModel->getToken();

        /**
         * Update the competition
         */
        if((isset($_GET['insertCompetition']) && $_SERVER['SERVER_ADDR'] == $this->ip) || (isset($_GET['insertCompetition']) && $_GET['token'] == $token->meta_value)){
            $dataModel = new \Model\dataModel\dataModel();
            $dataModel->insertCompetition();
        }
        /**
         * Update the standing (classement)
         */
        if((isset($_GET['updateStanding']) && $_SERVER['SERVER_ADDR'] == $this->ip) || (isset($_GET['updateStanding']) && $_GET['token'] == $token->meta_value)){
            $dataModel = new \Model\dataModel\dataModel();
            $dataModel->createStandingTable();
            $dataModel->insertUpdateStanding();
        }
        /**
         * Update the team
         */
        if((isset($_GET['updateTeam']) && $_SERVER['SERVER_ADDR'] == $this->ip) || (isset($_GET['updateTeam']) && $_GET['token'] == $token->meta_value)){
            $dataModel = new \Model\dataModel\dataModel();
            $dataModel->createTeamTable();
            $dataModel->insertUpdateTeams();
        }
        /**
         * Update the team
         */
        if((isset($_GET['updateMatch']) && $_SERVER['SERVER_ADDR'] == $this->ip) || (isset($_GET['updateMatch']) && $_GET['token'] == $token->meta_value)){
            $dataModel = new \Model\dataModel\dataModel();
            $dataModel->createMatchTable();
            $dataModel->insertUpdateMatch();
        }
        /**
         * Update the team
         */
        if((isset($_GET['cleanDB']) && $_SERVER['SERVER_ADDR'] == $this->ip) || (isset($_GET['cleanDB']) && $_GET['token'] == $token->meta_value)){
            $this->cleanDB();
        }
        /**
         * create admin apifoot plugin page
         */
        add_action('admin_menu', array($this, 'admin_apiFoot_setup_menu'));
        /**
         * return of form adminPage
         */
        if(isset($_POST['updateCompetition'])){
            $this->updateCompetition();
        }

        if(isset($_POST['updateKey'])){
            $this->updateApiKey();
        }


    }

    /**
     * Administration page of apiFoot plugin
     */
    public function admin_apiFoot_competition(){
        echo "<h1>".__('Selection des championnats!', 'apiFoot')."</h1>";
        $dataModel = new \Model\dataModel\dataModel();

        $allCompetition = $dataModel->getAllCompetitionsFromDB();
        include_once (__DIR__.'/../template/admin-competition.php');
    }

    /**
     * Administration page of apiFoot plugin
     */
    public function admin_apiFoot_key(){
        echo "<h1>".__('Clé api football-api.com !', 'apiFoot')."</h1>";
        $dataModel = new \Model\dataModel\dataModel();
        $api = $dataModel->getApiKey();
        include_once (__DIR__.'/../template/admin-key.php');
    }

    /**
     * Administration page of apiFoot plugin
     */
    public function admin_apiFoot_update(){
        echo "<h1>".__('Update !', 'apiFoot')."</h1>";
        $dataModel = new \Model\dataModel\dataModel();
        $token = $dataModel->getToken();
        include_once (__DIR__.'/../template/admin-update.php');
    }

    /**
     * Create admin page
     */
    public function admin_apiFoot_setup_menu(){
        add_menu_page( 'Api Foot Competition', 'Api Foot', 'manage_options', 'api-foot', array($this, 'admin_apiFoot_competition') );
        add_submenu_page( 'api-foot', 'Api Foot Key', 'Api Foot Key', 'manage_options', 'api-key', array($this, 'admin_apiFoot_key') );
        add_submenu_page( 'api-foot', 'Api Foot Update', 'Api Foot Update', 'manage_options', 'api-update', array($this, 'admin_apiFoot_update') );
    }

    /**
     * update the api key
     */
    public function updateApiKey(){
        $apiKey = $_POST['apiKey'];
        $dataModel = new \Model\dataModel\dataModel();
        $dataModel->updateApiKey($apiKey);
    }

    /**
     * Update the status of the cometition
     */
    public function updateCompetition(){
        $dataModel = new \Model\dataModel\dataModel();
        $dataModel->resetCompetitionToZero();
        foreach($_POST['competition'] as $competition_id){
            $dateFrom = $_POST['date_from'][$competition_id];
            $dateTo = $_POST['date_to'][$competition_id];
            $dataModel->updateCompetitionStatus($competition_id, 1, $dateFrom, $dateTo);
        }
    }

    /**
     * clean database from old entites
     */
    public function cleanDB(){
        $dataModel = new \Model\dataModel\dataModel();
        $dataModel->removeOldEntries();
    }

}