<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SubscribersList;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@emailcampaign.local'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Create sample subscriber lists
        $list1 = SubscribersList::firstOrCreate(
            ['slug' => 'general-list'],
            [
                'name' => 'General List',
                'description' => 'General subscribers',
                'is_active' => true,
            ]
        );

        $list2 = SubscribersList::firstOrCreate(
            ['slug' => 'premium-list'],
            [
                'name' => 'Premium Subscribers',
                'description' => 'Premium/VIP subscribers',
                'is_active' => true,
            ]
        );

        // Create sample email templates
        EmailTemplate::firstOrCreate(
            ['slug' => 'welcome-template'],
            [
                'name' => 'Welcome Email',
                'description' => 'Welcome email for new subscribers',
                'html_content' => $this->getWelcomeTemplate(),
                'is_active' => true,
                'created_by' => 1,
            ]
        );

        EmailTemplate::firstOrCreate(
            ['slug' => 'newsletter-template'],
            [
                'name' => 'Newsletter Template',
                'description' => 'General newsletter template',
                'html_content' => $this->getNewsletterTemplate(),
                'is_active' => true,
                'created_by' => 1,
            ]
        );

        EmailTemplate::firstOrCreate(
            ['slug' => 'promotional-template'],
            [
                'name' => 'Promotional Email',
                'description' => 'Promotional/offer email template',
                'html_content' => $this->getPromotionalTemplate(),
                'is_active' => true,
                'created_by' => 1,
            ]
        );

        echo "Database seeding completed successfully!\n";
    }

    private function getWelcomeTemplate()
    {
        return <<<'HTML'
<html>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h1 style="color: #667eea; margin-top: 0;">Welcome, {{name}}!</h1>
        <p>Thank you for subscribing to our email list. We're excited to stay in touch!</p>
        <p>You'll receive updates, tips, and exclusive offers from us.</p>
        <hr style="border: none; border-top: 1px solid #dee2e6;">
        <p style="color: #999; font-size: 12px; text-align: center;">
            <a href="{{unsubscribe_link}}" style="color: #667eea; text-decoration: none;">Unsubscribe</a>
        </p>
    </div>
</body>
</html>
HTML;
    }

    private function getNewsletterTemplate()
    {
        return <<<'HTML'
<html>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 8px;">
        <h2 style="color: #667eea;">Latest Newsletter</h2>
        <p>Hi {{first_name}},</p>
        <p>Here's what you need to know this week:</p>
        
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3>Article Title</h3>
            <p>Article preview text goes here...</p>
            <a href="#" style="color: #667eea; text-decoration: none; font-weight: bold;">Read More →</a>
        </div>
        
        <p style="color: #999; font-size: 12px;">
            <a href="{{unsubscribe_link}}" style="color: #667eea;">Unsubscribe</a>
        </p>
    </div>
</body>
</html>
HTML;
    }

    private function getPromotionalTemplate()
    {
        return <<<'HTML'
<html>
<body style="font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea, #764ba2); margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 8px;">
        <h1 style="color: #667eea; text-align: center; margin: 0;">Special Offer!</h1>
        <div style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 30px; border-radius: 5px; text-align: center; margin: 20px 0;">
            <h2 style="margin: 0;">Get 30% Off Today!</h2>
            <p style="font-size: 16px; margin: 15px 0;">Use code: SAVE30</p>
            <a href="#" style="background-color: white; color: #667eea; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">Shop Now</a>
        </div>
        <p style="color: #999; font-size: 12px; text-align: center;">
            <a href="{{unsubscribe_link}}" style="color: #667eea;">Unsubscribe</a>
        </p>
    </div>
</body>
</html>
HTML;
    }
}
