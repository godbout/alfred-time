<?php
/**
 *
 */
class Toggl
{
    private $message;
    private $apiToken;

    public function __construct($apiToken = null)
    {
        $this->message = '';
        $this->apiToken = $apiToken;
    }

    public function startTimer($description, $projectId, $tagNames)
    {
        $togglId = null;

        $url = 'https://www.toggl.com/api/v8/time_entries/start';

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . base64_encode($this->apiToken . ':api_token'),
        ];

        $item = [
            'time_entry' => [
                'description' => $description,
                'pid' => $projectId,
                'tags' => explode(', ', $tagNames),
                'created_with' => 'Alfred Time Workflow',
            ],
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($item, true));
        $response = curl_exec($ch);
        $lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || ($lastHttpCode < 200 || $lastHttpCode > 299)) {
            $this->message = '- Cannot start Toggl timer!';
        } else {
            $data = json_decode($response, true);
            $togglId = $data['data']['id'];
            $this->message = '- Toggl timer started';
        }

        return $togglId;
    }

    public function stopTimer($timerId = null)
    {
        $res = false;

        $url = 'https://www.toggl.com/api/v8/time_entries/' . $timerId . '/stop';

        $headers = [
            "Content-type: application/json",
            "Accept: application/json",
            'Authorization: Basic ' . base64_encode($this->apiToken . ':api_token'),
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            $this->message = '- Could not stop Toggl timer!';
        } else {
            $this->message = '- Toggl timer stopped';
            $res = true;
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
}
