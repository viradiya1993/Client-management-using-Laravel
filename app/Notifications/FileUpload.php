<?php

namespace App\Notifications;

use App\Project;
use App\ProjectFile;
use App\Setting;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class FileUpload extends Notification implements ShouldQueue
{
    use Queueable, SmtpSettings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $file;
    private $project;
    private $global;
    public function __construct(ProjectFile $file)
    {
        $this->file = $file;
        $this->project = Project::find($this->file->project_id);
        $this->setMailConfigs();
        $this->global = Setting::first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('/');

        return (new MailMessage)
            ->subject(__('email.fileUpload.subject') .' '. $this->project->project_name . ' - ' . config('app.name'))
            ->greeting(__('email.hello') . ' ' . ucwords($notifiable->name) . '!')
            ->line(__('email.fileUpload.subject')  . ucwords($this->project->project_name))
            ->line(__('modules.projects.fileName') . ' - ' . $this->file->filename)
            ->line(__('app.date') . ' - ' . $this->file->created_at->format($this->global->date_format))
            ->action(__('email.loginDashboard'), route('login'))
            ->line(__('email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
