<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\ChatType;

class ChatTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ChatType::create([
            "chat_type_id" => (string) Str::uuid(),
            "chat_type_name" => ChatType::CHAT_NAME_MESSAGE,
            "chat_type_action" => ChatType::CHAT_ACTION_SENT,
            "chat_type_desc" => "General messages"
        ]);
        ChatType::create([
            "chat_type_id" => (string) Str::uuid(),
            "chat_type_name" => ChatType::CHAT_NAME_MESSAGE,
            "chat_type_action" => ChatType::CHAT_ACTION_EDITED,
            "chat_type_desc" => "General messages"
        ]);
        ChatType::create([
            "chat_type_id" => (string) Str::uuid(),
            "chat_type_name" => ChatType::CHAT_NAME_MESSAGE,
            "chat_type_action" => ChatType::CHAT_ACTION_DELETED,
            "chat_type_desc" => "General messages"
        ]);

        // contract
        ChatType::create([
            "chat_type_id" => (string) Str::uuid(),
            "chat_type_name" => ChatType::CHAT_NAME_CONTRACT,
            "chat_type_action" => ChatType::CHAT_ACTION_SENT,
            "chat_type_desc" => "Contract chat log"
        ]);
        ChatType::create([
            "chat_type_id" => (string) Str::uuid(),
            "chat_type_name" => ChatType::CHAT_NAME_CONTRACT,
            "chat_type_action" => ChatType::CHAT_ACTION_ACCEPTED,
            "chat_type_desc" => "Contract chat log"
        ]);
        ChatType::create([
            "chat_type_id" => (string) Str::uuid(),
            "chat_type_name" => ChatType::CHAT_NAME_CONTRACT,
            "chat_type_action" => ChatType::CHAT_ACTION_CANCELED,
            "chat_type_desc" => "Contract chat log"
        ]);
        ChatType::create([
            "chat_type_id" => (string) Str::uuid(),
            "chat_type_name" => ChatType::CHAT_NAME_CONTRACT,
            "chat_type_action" => ChatType::CHAT_ACTION_DECLINED,
            "chat_type_desc" => "Contract chat log"
        ]);
        ChatType::create([
            "chat_type_id" => (string) Str::uuid(),
            "chat_type_name" => ChatType::CHAT_NAME_CONTRACT,
            "chat_type_action" => ChatType::CHAT_ACTION_ENDED,
            "chat_type_desc" => "Contract chat log"
        ]);


        // invoice
        ChatType::create([
            "chat_type_id" => (string) Str::uuid(),
            "chat_type_name" => ChatType::CHAT_NAME_INVOICE,
            "chat_type_action" => ChatType::CHAT_ACTION_SENT,
            "chat_type_desc" => "Invoice chat log"
        ]);


        // timetrack
        ChatType::create([
            "chat_type_id" => (string) Str::uuid(),
            "chat_type_name" => ChatType::CHAT_NAME_TIMETRACK,
            "chat_type_action" => ChatType::CHAT_ACTION_SENT,
            "chat_type_desc" => "Time track chat log"
        ]);

        // review
        ChatType::create([
            "chat_type_id" => (string) Str::uuid(),
            "chat_type_name" => ChatType::CHAT_NAME_REVIEW,
            "chat_type_action" => ChatType::CHAT_ACTION_SENT,
            "chat_type_desc" => "Send the Review"
        ]);
    }
}
