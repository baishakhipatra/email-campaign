<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\SmtpSetting;

class CampaignMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $campaign;
    public $subscriber;

    public function __construct($campaign, $subscriber)
    {
        $this->campaign = $campaign;
        $this->subscriber = $subscriber;
    }

    public function build()
    {
        $html = $this->campaign->template->html_content;

        $html = str_replace(
            ['{{name}}', '{{email}}', '{{first_name}}', '{{last_name}}', '{{unsubscribe_link}}'],
            [
                ucwords($this->subscriber->name),
                $this->subscriber->email,
                $this->subscriber->first_name,
                $this->subscriber->last_name,
                route('unsubscribe', ['token' => $this->subscriber->unsubscribe_token])
            ],
            $html
        );

        $smtp = SmtpSetting::getActive();

        return $this->from(
                $smtp->username,             
                ucwords($this->campaign->from_name)
            )
            ->replyTo(                        
                $this->campaign->from_email
            )
            ->subject(ucwords($this->campaign->subject))
            ->html($html);
    }
}
