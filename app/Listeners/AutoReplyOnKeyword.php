<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Kstmostofa\LaravelWhatsApp\Events\MessageReceived as CloudMessageReceived;
use Kstmostofa\LaravelWhatsApp\Events\Web\MessageReceived as WebMessageReceived;
use Kstmostofa\LaravelWhatsApp\Facades\WhatsApp;
use Throwable;

class AutoReplyOnKeyword
{
    private const KEYWORDS = ['2', '۲', '٢'];

    private const REPLY = <<<'TEXT'
سلام من علی قاسم زاده هستم صبر کنید
وب سایت فروش 1860
TEXT;

    public function handleWeb(WebMessageReceived $event): void
    {
        if ($event->fromMe()) {
            return;
        }

        $from = $event->from();

        if ($from === null || $from === '') {
            return;
        }

        if (! $this->matches((string) $event->body())) {
            return;
        }

        try {
            WhatsApp::web($event->sessionId)->messages()->sendText($from, self::REPLY);
        } catch (Throwable $e) {
            Log::error('WhatsApp auto-reply failed (web).', [
                'session' => $event->sessionId,
                'from' => $from,
                'exception' => $e,
            ]);
        }
    }

    public function handleCloud(CloudMessageReceived $event): void
    {
        $from = $event->from();

        if ($from === null || $from === '') {
            return;
        }

        if (! $this->matches((string) $event->text())) {
            return;
        }

        try {
            WhatsApp::messages()->sendText($from, self::REPLY, false, $event->messageId());
        } catch (Throwable $e) {
            Log::error('WhatsApp auto-reply failed (cloud).', [
                'from' => $from,
                'exception' => $e,
            ]);
        }
    }

    private function matches(string $body): bool
    {
        return in_array(trim($body), self::KEYWORDS, true);
    }
}
