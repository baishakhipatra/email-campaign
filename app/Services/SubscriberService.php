<?php

namespace App\Services;

use App\Models\Subscriber;
use App\Models\SubscribersList;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SubscriberService
{
    /**
     * Create a subscriber
     */
    public function createSubscriber($data)
    {
        $subscriber = Subscriber::create([
            'name' => $data['name'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'birthday_date' => $data['birthday_date'] ?? null,
            'anniversary_date' => $data['anniversary_date'] ?? null,
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
    // public function importFromCsv($file, $listId)
    // {
    //     $imported = 0;
    //     $skipped = 0;
    //     $errors = [];

    //     if (($handle = fopen($file, 'r')) !== false) {
    //         $headers = fgetcsv($handle, 1000, ',');
    //         $emailKey = array_search('email', array_map('strtolower', $headers));

    //         if ($emailKey === false) {
    //             throw new \Exception('CSV must contain an "email" column');
    //         }

    //         while (($data = fgetcsv($handle, 1000, ',')) !== false) {
    //             try {
    //                 $email = $data[$emailKey];

    //                 // Check if email exists
    //                 if (Subscriber::where('email', $email)->exists()) {
    //                     $skipped++;
    //                     continue;
    //                 }

    //                 // Prepare custom fields
    //                 $customFields = [];
    //                 foreach ($headers as $index => $header) {
    //                     if ($header !== 'email' && $header !== 'name') {
    //                         $customFields[$header] = $data[$index] ?? null;
    //                     }
    //                 }

    //                 $subscriber = Subscriber::create([
    //                     'email' => $email,
    //                     'name' => $data[array_search('name', $headers)] ?? null,
    //                     'custom_fields' => !empty($customFields) ? $customFields : null,
    //                     'unsubscribe_token' => Str::random(40),
    //                 ]);

    //                 if ($listId) {
    //                     $subscriber->lists()->attach($listId);
    //                 }

    //                 $imported++;
    //             } catch (\Exception $e) {
    //                 $errors[] = "Row " . ($imported + $skipped + 1) . ": " . $e->getMessage();
    //             }
    //         }

    //         fclose($handle);
    //     }

    //     return [
    //         'imported' => $imported,
    //         'skipped' => $skipped,
    //         'errors' => $errors,
    //     ];
    // }

    public function importFromCsv($file, $listId)
    {
        $imported = 0;
        $skipped = 0;
        $errors = [];

        if (($handle = fopen($file, 'r')) !== false) {

            $headers = fgetcsv($handle, 1000, ',');
            $headersLower = array_map('strtolower', $headers);

            $emailKey = array_search('email', $headersLower);
            $nameKey  = array_search('name', $headersLower);
            $phoneKey = array_search('phone', $headersLower);
            $birthKey = array_search('birthday_date', $headersLower);
            $anniKey  = array_search('anniversary_date', $headersLower);

            if ($emailKey === false) {
                throw new \Exception('CSV must contain an "email" column');
            }

            if ($nameKey === false) {
                throw new \Exception('CSV must contain a "name" column');
            }

            $rowNumber = 1;

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $rowNumber++;

                if (empty(array_filter($data))) {
                    continue;
                }

                try {
                    $email = $data[$emailKey] ?? null;
                    $name  = $data[$nameKey] ?? null;
                    $phone = $phoneKey !== false ? ($data[$phoneKey] ?? null) : null;
                    $birthday = $birthKey !== false ? ($data[$birthKey] ?? null) : null;
                    $anniversary = $anniKey !== false ? ($data[$anniKey] ?? null) : null;

                    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        throw new \Exception("Invalid email");
                    }

                    if (!$name) {
                        throw new \Exception("Name is required");
                    }

                    if ($phone && !preg_match('/^[0-9]{10}$/', $phone)) {
                        throw new \Exception("Phone must be 10 digits");
                    }

                    if (!empty($birthday)) {
                        try {
                            $birthday = Carbon::createFromFormat('d-m-Y', $birthday)->format('Y-m-d');

                            if (Carbon::parse($birthday)->isFuture()) {
                                throw new \Exception('Birthday must be before today');
                            }
                        } catch (\Exception $e) {
                            throw new \Exception('Invalid birthday format (use DD-MM-YYYY)');
                        }
                    }

                    if (!empty($anniversary)) {
                        try {
                            $anniversary = Carbon::createFromFormat('d-m-Y', $anniversary)->format('Y-m-d');
                        } catch (\Exception $e) {
                            throw new \Exception('Invalid anniversary format (use DD-MM-YYYY)');
                        }
                    }

                    if (Subscriber::where('email', $email)->exists()) {
                        $skipped++;
                        $errors[] = "Row {$rowNumber}: Email already exists ($email)";
                        continue;
                    }

                    $subscriber = Subscriber::create([
                        'email' => $email,
                        'name' => $name,
                        'phone' => $phone,
                        'birthday_date' => $birthday ?? null,
                        'anniversary_date' => $anniversary ?? null,
                        'unsubscribe_token' => Str::random(40),
                    ]);

                    if ($listId) {
                        $subscriber->lists()->attach($listId);
                    }

                    $imported++;

                } catch (\Exception $e) {
                    //dd($e->getMessage());
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
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
