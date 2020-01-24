<?php

namespace Laravolt\Mailkeeper;

use Illuminate\Console\Command;
use Illuminate\Mail\Message;

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

        $this->info(sprintf('Sending %d emails...', $mails->count()));
        foreach ($mails as $mail) {
            try {
                \Illuminate\Support\Facades\Mail::send([], [], function (Message $message) use ($mail) {
                    $message->subject($mail->subject)
                        ->from($mail->from)
                        ->to($mail->to)
                        ->priority($mail->priority)
                        ->setBody($mail->body, $mail->content_type);

                    if ($mail->sender) {
                        $message->sender($mail->sender);
                    }

                    if ($mail->cc) {
                        $message->cc($mail->cc);
                    }

                    if ($mail->bcc) {
                        $message->bcc($mail->bcc);
                    }

                    if ($mail->reply_to) {
                        $message->replyTo($mail->reply_to);
                    }
                });
            } catch (\Swift_SwiftException $e) {
                $this->error($e->getMessage());
                $mail->error = $e->getMessage();
                $mail->save();
            } finally {
                $mail->delete();
            }
        }

        $this->info('Finished');
    }
}
