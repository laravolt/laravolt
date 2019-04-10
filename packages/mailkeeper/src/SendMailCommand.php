<?php

namespace Laravolt\Mailkeeper;

use Illuminate\Console\Command;
use Illuminate\Mail\MailServiceProvider;

class SendMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravolt:send-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail stored in database';

    public function __construct()
    {
        parent::__construct();

        // disable mailkeeper so we can send email using the normal way
        config()->set('laravolt.mailkeeper.enabled', false);
        (new ServiceProvider(app()))->register();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $take = config('laravolt.mailkeeper.take');
        $mails = Mail::take($take)->get();

        $this->info(sprintf("Sending %d emails...", $mails->count()));
        foreach ($mails as $mail) {
            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($mail) {
                $message
                    ->subject($mail->subject)
                    ->from($mail->from)
                    ->sender($mail->sender)
                    ->to($mail->to)
                    ->cc($mail->cc)
                    ->bcc($mail->bcc)
                    ->replyTo($mail->reply_to)
                    ->priority($mail->priority)
                    ->setBody($mail->body, $mail->content_type);
            });

            $mail->delete();
        }

        $this->info("Finished");
    }
}
