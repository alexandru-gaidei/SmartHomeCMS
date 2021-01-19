<?php

namespace App\Traits;

use App\Action as ActionModel;
use App\Events\ActionUINotify;
use App\Events\HistoryFailOcured;
use App\History;
use App\Notifications\ActionMail;
use GuzzleHttp\Client;

trait Action
{
    public function do()
    {
        switch ($this->type) {
            case ActionModel::TYPE_UI:
                $this->doActionTypeUI();
                break;
            case ActionModel::TYPE_MAIL:
                $this->doActionTypeMail();
                break;
            case ActionModel::TYPE_HTTP_GET:
                $this->doActionTypeHttp();
                break;
            default:
                break;
        }
    }

    private function doActionTypeUI()
    {
        $sensor = $this->sensor;
        $data = [
            'group_id' => $this->sensor->group_id,
            'sensor' => [
                'name' => $sensor->name,
                'value' => $sensor->value,
                'min_value' => $sensor->min_value,
                'max_value' => $sensor->max_value,
            ],
            'action_name' => $this->name,
            'datetime' => now()->format('m/d/Y H:i:s'),
        ];
        event(new ActionUINotify($data));
    }

    private function doActionTypeMail()
    {
        $action = $this;
        $this->sensor->group->users->each(function ($user) use ($action) {
            $user->notify(new ActionMail($action));
        });

        $action->history()->create([
            'status' => History::OK,
            'data' => '',
            'ocurrence_at' => now(),
        ]);
    }

    private function doActionTypeHttp()
    {
        $client = new Client();

        try {
            $response = $client->get($this->subject);
            $data = $response->getBody()->getContents();
            $status = History::OK;
        } catch(\Exception $err) {
            $data = $err->getMessage();
            $status = History::FAIL;
            event(new HistoryFailOcured());
        }

        $this->history()->create([
            'status' => $status,
            'data' => $data,
            'ocurrence_at' => now(),
        ]);
    }
}
