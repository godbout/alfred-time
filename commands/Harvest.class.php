<?php
/**
 *
 */
class Harvest
{
    private $message;
    private $domain;
    private $apiToken;

    public function __construct($domain = null, $apiToken = null)
    {
        $this->message = '';
        $this->domain = $domain;
        $this->apiToken = $apiToken;
    }

    public function startTimer($description, $projectId, $taskId)
    {
        $harvestId = null;

        $url = 'https://' . $this->domain . '.harvestapp.com/daily/add';

        $base64Token = $this->apiToken;

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . $base64Token,
        ];

        $item = [
            'notes' => $description,
            'project_id' => $projectId,
            'task_id' => $taskId,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($item, true));
        $response = curl_exec($ch);
        $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $lastHttpCode !== 201) {
            $this->message = '- Cannot start Harvest timer!';
        } else {
            $data = json_decode($response, true);
            $harvestId = $data['id'];
            $this->message = '- Harvest timer started';
        }

        return $harvestId;
    }

    public function stopTimer($timerId = null)
    {
        $res = false;

        if ($this->isTimerRunning($timerId) === true) {
            $url = 'https://' . $this->domain . '.harvestapp.com/daily/timer/' . $timerId;

            $base64Token = $this->apiToken;

            $headers = [
                "Content-type: application/json",
                "Accept: application/json",
                'Authorization: Basic ' . $base64Token,
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response === false || $lastHttpCode !== 200) {
                $this->message = '- Could not stop Harvest timer!';
            } else {
                $this->message = '- Harvest timer stopped';
                $res = true;
            }
        } else {
            $this->message = '- Harvest timer was not running';
        }

        return $res;
    }

    public function getLastMessage()
    {
        return $this->message;
    }

    public function deleteTimer($timerId = null)
    {
        $res = false;

        $url = 'https://www.toggl.com/api/v8/time_entries/' . $timerId;

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . base64_encode($this->apiToken . ':api_token'),
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $lastHttpCode !== 200) {
            $this->message = '- Could not delete Toggl timer!';
        } else {
            $this->message = '- Toggl timer deleted';
            $res = true;
        }

        return $res;
    }

    public function getRecentTimers()
    {
        $timers = [];

        $url = 'https://www.toggl.com/api/v8/time_entries';

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . base64_encode($this->apiToken . ':api_token'),
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response !== false && $lastHttpCode === 200) {
            $timers = json_decode($response, true);
        }

        return array_reverse($timers);
    }

    public function getOnlineData()
    {
        $data = [];

        $url = 'https://www.toggl.com/api/v8/me?with_related_data=true';

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . base64_encode($this->apiToken . ':api_token'),
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || ($lastHttpCode < 200 || $lastHttpCode > 299)) {
            $this->message = '- Cannot get Toggl online data!';
        } else {
            $data = json_decode($response, true);
            $this->message = '- Toggl data cached';
        }

        return $data;
    }

    private function isTimerRunning($timerId)
    {
        $res = false;

        $url = 'https://' . $this->domain . '.harvestapp.com/daily/show/' . $timerId;

        $base64Token = $this->apiToken;

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . $base64Token,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response !== false && $lastHttpCode === 200) {
            $data = json_decode($response, true);
            if (isset($data['timer_started_at']) === true) {
                $res = true;
            }
        }

        return $res;
    }
}
