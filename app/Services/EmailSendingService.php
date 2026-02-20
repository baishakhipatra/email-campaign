<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class EmailSendingService
{
    /**
     * Send email for a campaign
     */
    public function sendCampaignEmail(Campaign $campaign, $subscriberEmail, $subscriberData = [])
    {
        try {
            // Get active SMTP settings
            $smtpSetting = SmtpSetting::getActive();
            if (!$smtpSetting) {
                throw new \Exception('No active SMTP configuration found');
            }

            // Configure mail driver dynamically
            Config::set('mail.mailers.smtp', $smtpSetting->toMailerConfig());

            // Prepare email content with variables
            $emailContent = $this->replaceVariables(
                $campaign->template->html_content,
                $subscriberData
            );

            // Add tracking pixel
            $trackingPixel = $this->generateTrackingPixel($campaign->id, $subscriberEmail);
            $emailContent .= $trackingPixel;

            // Replace links with tracking URLs
            $emailContent = $this->replaceLinksWithTracking($emailContent, $campaign->id, $subscriberEmail);

            // Send email
            Mail::html($emailContent, function ($message) use ($campaign, $subscriberEmail) {
                $message->to($subscriberEmail)
                    ->from($campaign->from_email, $campaign->from_name)
                    ->subject($campaign->subject);
            });

            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Replace template variables with actual values
     */
    private function replaceVariables($content, $data)
    {
        $content = str_replace('{{name}}', $data['name'] ?? '', $content);
        $content = str_replace('{{email}}', $data['email'] ?? '', $content);
        $content = str_replace('{{unsubscribe_link}}', $data['unsubscribe_link'] ?? '', $content);
        $content = str_replace('{{first_name}}', $data['first_name'] ?? '', $content);
        $content = str_replace('{{last_name}}', $data['last_name'] ?? '', $content);
        
        return $content;
    }

    /**
     * Generate tracking pixel for open tracking
     */
    private function generateTrackingPixel($campaignId, $email)
    {
        $emailLog = EmailLog::where('campaign_id', $campaignId)
            ->where('recipient_email', $email)
            ->first();

        if (!$emailLog) {
            return '';
        }

        $trackingUrl = route('tracking.open', $emailLog->tracking_token);
        return '<img src="' . $trackingUrl . '" width="1" height="1" alt="" />';
    }

    /**
     * Replace links with tracking URLs
     */
    private function replaceLinksWithTracking($content, $campaignId, $email)
    {
        $emailLog = EmailLog::where('campaign_id', $campaignId)
            ->where('recipient_email', $email)
            ->first();

        if (!$emailLog) {
            return $content;
        }

        // Match all href URLs
        return preg_replace_callback(
            '/href=["\']([^"\']+)["\']/',
            function ($matches) use ($emailLog) {
                $originalUrl = $matches[1];
                
                // Skip unsubscribe links and tracking links
                if (strpos($originalUrl, 'unsubscribe') !== false || 
                    strpos($originalUrl, 'tracking') !== false) {
                    return $matches[0];
                }

                $trackingUrl = route('tracking.click', [
                    'token' => \Illuminate\Support\Str::random(40),
                    'email_log_id' => $emailLog->id,
                    'url' => base64_encode($originalUrl),
                ]);

                return 'href="' . $trackingUrl . '"';
            },
            $content
        );
    }
}
