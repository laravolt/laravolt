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

    public function __construct() {
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
        foreach($mails as $mail) {
            $from['email'] = key($mail->from);
            $from['name'] = head($mail->from);
            $to['email'] = key($mail->to);
            $to['name'] = head($mail->to);

            \Illuminate\Support\Facades\Mail::html($mail->body, function ($message) use ($from, $to, $mail) {
                $message->to($to['email'], $to['name']);
                $message->from($from['email'], $from['name']);
                $message->subject($mail->subject);
            });

            $mail->delete();
        }

        $this->info("Finished");
    }
}
