<?php

namespace App\Services;

use App\Models\Subscriber;
use App\Models\SubscribersList;
use Illuminate\Support\Str;

class SubscriberService
{
    /**
     * Create a subscriber
     */
    public function createSubscriber($data)
    {
        $subscriber = Subscriber::create([
            'email' => $data['email'],
            'name' => $data['name'] ?? null,
            'status' => $data['status'] ?? 'active',
            'custom_fields' => $data['custom_fields'] ?? null,
            'unsubscribe_token' => Str::random(40),
        ]);

        // Attach to lists if provided
        if (isset($data['lists'])) {
            $subscriber->lists()->attach($data['lists']);
        }

        return $subscriber;
    }

    /**
     * Import subscribers from CSV
     */
    public function importFromCsv($file, $listId)
    {
        $imported = 0;
        $skipped = 0;
        $errors = [];

        if (($handle = fopen($file, 'r')) !== false) {
            $headers = fgetcsv($handle, 1000, ',');
            $emailKey = array_search('email', array_map('strtolower', $headers));

            if ($emailKey === false) {
                throw new \Exception('CSV must contain an "email" column');
            }

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                try {
                    $email = $data[$emailKey];

                    // Check if email exists
                    if (Subscriber::where('email', $email)->exists()) {
                        $skipped++;
                        continue;
                    }

                    // Prepare custom fields
                    $customFields = [];
                    foreach ($headers as $index => $header) {
                        if ($header !== 'email' && $header !== 'name') {
                            $customFields[$header] = $data[$index] ?? null;
                        }
                    }

                    $subscriber = Subscriber::create([
                        'email' => $email,
                        'name' => $data[array_search('name', $headers)] ?? null,
                        'custom_fields' => !empty($customFields) ? $customFields : null,
                        'unsubscribe_token' => Str::random(40),
                    ]);

                    if ($listId) {
                        $subscriber->lists()->attach($listId);
                    }

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($imported + $skipped + 1) . ": " . $e->getMessage();
                }
            }

            fclose($handle);
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Export subscribers to CSV
     */
    public function exportToCsv(SubscribersList $list)
    {
        $subscribers = $list->subscribers;

        $csv = "Email,Name,Status,Subscribed At\n";
        foreach ($subscribers as $subscriber) {
            $csv .= sprintf(
                '"%s","%s","%s","%s"' . "\n",
                $subscriber->email,
                $subscriber->name ?? '',
                $subscriber->status,
                $subscriber->subscribed_at
            );
        }

        return $csv;
    }

    /**
     * Unsubscribe a subscriber
     */
    public function unsubscribe($unsubscribeToken)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $unsubscribeToken)->first();
        
        if ($subscriber) {
            $subscriber->unsubscribe();
            return true;
        }

        return false;
    }
}
