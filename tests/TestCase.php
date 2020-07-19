<?php

namespace Tests;

use Dotenv\Dotenv;
use Godbout\Alfred\Time\Workflow;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected $workflowDataFolder = __DIR__ . '/mo.com.sleeplessmind.time';

    protected $configFile = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpWorkflowDataFolder();

        $this->setUpConfigFilePath();

        $this->resetWorkflowToDefaultSettings();

        $this->loadSecretApikeys();
    }

    private function setUpWorkflowDataFolder()
    {
        putenv("alfred_workflow_data={$this->workflowDataFolder}");
    }

    private function setUpConfigFilePath()
    {
        $this->configFile = $this->workflowDataFolder . '/config.json';
    }

    private function resetWorkflowToDefaultSettings()
    {
        $this->resetWorkflowSingleton();

        $this->resetConfigToDefaultSettings();

        $this->resetEnvVariables();

        $this->resetScriptArguments();
    }

    private function resetWorkflowSingleton()
    {
        Workflow::destroy();
    }

    private function resetConfigToDefaultSettings()
    {
        $this->disableAllTimerServices();

        $this->togglApikey('');
    }

    private function resetEnvVariables()
    {
        putenv('next=');
    }

    private function resetScriptArguments()
    {
        global $argv;
        $argv = [];
    }

    private function loadSecretApikeys()
    {
        if (file_exists(__DIR__ . '/../.env')) {
            Dotenv::create(__DIR__ . '/..//')->load();
        }
    }

    protected function disableAllTimerServices()
    {
        Workflow::disableAllServices();
    }

    protected function togglApikey($apikey = 'e695b4364ad1ea7200035fec1bbc87cf')
    {
        Workflow::getConfig()->write('toggl.api_token', $apikey);
    }

    protected function harvestApitoken($apitoken = '1153865.pt.Vjxherj4YPfPiEhTp3jORa3OZYIK15VD2wkAPmrA1Y7uOBUzsi-WtFznKGxJIuc2rnnFDxWV-lj946fGI42hNQ')
    {
        Workflow::getConfig()->write('harvest.api_token', $apitoken);
    }

    protected function everHourApikey($apikey = '507f-ef41-c355b1-992023-06d0dff9')
    {
        Workflow::getConfig()->write('everhour.api_token', $apikey);
    }

    protected function clockifyApikey($apikey = 'XxBtwrIBtgnj3kPX')
    {
        Workflow::getConfig()->write('clockify.api_token', $apikey);
    }

    protected function harvestAccountId($accountId = '987654')
    {
        Workflow::getConfig()->write('harvest.account_id', $accountId);
    }

    protected function reachWorkflowInitialMenu($envVariables = [], $arguments = [])
    {
        return $this->reachWorkflowMenu($envVariables, $arguments);
    }

    protected function reachWorkflowSetupMenu()
    {
        return $this->reachWorkflowMenu('next=setup');
    }

    protected function reachTogglSetupMenu()
    {
        return $this->reachWorkflowMenu('next=toggl_setup');
    }

    protected function reachHarvestSetupMenu()
    {
        return $this->reachWorkflowMenu('next=harvest_setup');
    }

    protected function reachEverhourSetupMenu()
    {
        return $this->reachWorkflowMenu('next=everhour_setup');
    }

    protected function reachClockifySetupMenu()
    {
        return $this->reachWorkflowMenu('next=clockify_setup');
    }

    protected function reachTogglApikeySetupMenu()
    {
        return $this->reachWorkflowMenu('next=toggl_setup_apikey');
    }

    protected function reachHarvestCredentialsSetupMenu()
    {
        return $this->reachWorkflowMenu('next=harvest_setup_credentials');
    }

    protected function reachEverhourApikeySetupMenu()
    {
        return $this->reachWorkflowMenu('next=everhour_setup_apikey');
    }

    protected function reachClockifyApikeySetupMenu()
    {
        return $this->reachWorkflowMenu('next=clockify_setup_apikey');
    }

    protected function reachTogglStateSavedMenu($envVariables = [])
    {
        $envVariables = array_merge(['next=toggl_setup_state'], (array) $envVariables);

        return $this->reachWorkflowMenu($envVariables);
    }

    protected function reachHarvestStateSavedMenu($envVariables = [])
    {
        $envVariables = array_merge(['next=harvest_setup_state'], (array) $envVariables);

        return $this->reachWorkflowMenu($envVariables);
    }

    protected function reachEverhourStateSavedMenu($envVariables = [])
    {
        $envVariables = array_merge(['next=everhour_setup_state'], (array) $envVariables);

        return $this->reachWorkflowMenu($envVariables);
    }

    protected function reachClockifyStateSavedMenu($envVariables = [])
    {
        $envVariables = array_merge(['next=clockify_setup_state'], (array) $envVariables);

        return $this->reachWorkflowMenu($envVariables);
    }

    protected function reachTogglApikeySavedMenu($envVariables = [])
    {
        $envVariables = array_merge(['next=toggl_setup_apikey_save'], (array) $envVariables);

        return $this->reachWorkflowMenu($envVariables);
    }

    protected function reachHarvestApitokenSavedMenu($envVariables = [])
    {
        $envVariables = array_merge(['next=harvest_setup_apitoken_save'], (array) $envVariables);

        return $this->reachWorkflowMenu($envVariables);
    }

    protected function reachEverhourApikeySavedMenu($envVariables = [])
    {
        $envVariables = array_merge(['next=everhour_setup_apikey_save'], (array) $envVariables);

        return $this->reachWorkflowMenu($envVariables);
    }

    protected function reachClockifyApikeySavedMenu($envVariables = [])
    {
        $envVariables = array_merge(['next=clockify_setup_apikey_save'], (array) $envVariables);

        return $this->reachWorkflowMenu($envVariables);
    }

    protected function reachHarvestAccountIdSavedMenu($envVariables = [])
    {
        $envVariables = array_merge(['next=harvest_setup_account_id_save'], (array) $envVariables);

        return $this->reachWorkflowMenu($envVariables);
    }

    protected function reachWorkflowChooseProjectMenu()
    {
        return $this->reachWorkflowMenu('next=choose_project');
    }

    protected function reachWorkflowChooseTagMenu()
    {
        return $this->reachWorkflowMenu('next=choose_tag');
    }

    protected function reachWorkflowGoAction($envVariables = [])
    {
        $envVariables = array_merge(['next=do'], (array) $envVariables);

        return $this->reachWorkflowAction($envVariables);
    }

    private function reachWorkflowAction($envVariables = [], $arguments = [])
    {
        $this->buildWorkflowWorld($envVariables, $arguments);

        return Workflow::do();
    }

    private function reachWorkflowMenu($envVariables = [], $arguments = [])
    {
        $this->buildWorkflowWorld($envVariables, $arguments);

        return Workflow::currentMenu();
    }

    protected function setTogglTimerAttributes()
    {
        putenv('timer_description=description');
        putenv('timer_project_id=' . getenv('TOGGL_PROJECT_ID'));
        putenv('timer_tag=' . getenv('TOGGL_TAG_NAME'));
    }

    protected function setHarvestTimerAttributes()
    {
        putenv('timer_description=description');
        putenv('timer_project_id=' . getenv('HARVEST_PROJECT_ID'));
        putenv('timer_tag_id=' . getenv('HARVEST_TAG_ID'));
    }

    protected function setEverhourTimerAttributes()
    {
        putenv('timer_description=description');
        putenv('timer_project_id=' . getenv('EVERHOUR_PROJECT_ID'));
        putenv('timer_tag_id=' . getenv('EVERHOUR_TAG_ID'));
    }

    protected function setClockifyTimerAttributes()
    {
        putenv('timer_description=description');
        putenv('timer_workspace_id=' . getenv('CLOCKIFY_WORKSPACE_ID'));
        putenv('timer_project_id=' . getenv('CLOCKIFY_PROJECT_ID'));
    }

    private function buildWorkflowWorld($envVariables = [], $arguments = [])
    {
        $this->buildEnvironmentVariables((array) $envVariables);

        $this->buildArguments((array) $arguments);
    }

    private function buildEnvironmentVariables(array $envVariables = [])
    {
        foreach ($envVariables as $envVariable) {
            putenv($envVariable);
        }
    }

    private function buildArguments(array $arguments = [])
    {
        global $argv;

        $argv[0] = 'src/app.php';

        foreach ($arguments as $argument) {
            $argv[] = $argument;
        }
    }
}
