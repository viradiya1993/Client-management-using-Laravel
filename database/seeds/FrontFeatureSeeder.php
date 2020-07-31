<?php

use Illuminate\Database\Seeder;

class FrontFeatureSeeder extends Seeder
{

    public function run()
    {
        $frontDetails =  \App\FrontDetail::first();

        $frontDetails->primary_color          = '#2f20db';
        $frontDetails->task_management_title  = 'Task Management';
        $frontDetails->task_management_detail = 'Manage your projects and your talent in a single system, resulting in empowered teams, satisfied clients, and increased profitability.';
        $frontDetails->manage_bills_title     = 'Manages All Your Bills';
        $frontDetails->manage_bills_detail    = 'Manage your Automate billing and revenue recognition to streamline the contract-to-cash cycle.';
        $frontDetails->teamates_title         = 'Manages All Your Bills';
        $frontDetails->teamates_detail        = 'Manage your Automate billing and revenue recognition to streamline the contract-to-cash cycle.';
        $frontDetails->favourite_apps_title   = 'Integrate With Your Favourite Apps.';
        $frontDetails->favourite_apps_detail  = 'Our app gives you the added advantage of several other third party apps through seamless integrations.';
        $frontDetails->cta_title              = 'Managing Business Has Never Been So Easy.';
        $frontDetails->cta_detail             = 'Don\'t hesitate, Our experts will show you how our application can streamline the way your team works.';
        $frontDetails->client_title           = 'We Build Trust';
        $frontDetails->client_detail          = 'More Than 700 People Use Our Product.';
        $frontDetails->testimonial_title      = 'Loved By Businesses, And Individuals Across The Globe';
        $frontDetails->faq_title              = 'Frequently Asked Questions';
        $frontDetails->footer_copyright_text  = 'Copyright © 2020. All Rights Reserved';
        $frontDetails->save();

        // Team Management Section
        $feature = new \App\Feature();
        $feature->title = 'Track Projects';
        $feature->description = '<span style="color: rgb(68, 68, 68); font-family: Lato, sans-serif; font-size: 16px; text-align: center;">Keep a track of all your projects in the most simple way.</span>';
        $feature->icon = 'fas fa-desktop';
        $feature->type = 'task';
        $feature->save();

        $feature = new \App\Feature();
        $feature->title = 'Add Members';
        $feature->description = '<span style="color: rgb(68, 68, 68); font-family: Lato, sans-serif; font-size: 16px; text-align: center;">Add members to your projects and keep them in sync with the progress.</span>';
        $feature->icon = 'fas fa-users';
        $feature->type = 'task';
        $feature->save();

        $feature = new \App\Feature();
        $feature->title = 'Assign Tasks';
        $feature->description = '<span style="color: rgb(68, 68, 68); font-family: Lato, sans-serif; font-size: 16px; text-align: center;">Your website is fully responsive, it will work on any device, desktop, tablet and mobile.</span>';
        $feature->icon = 'fas fa-list';
        $feature->type = 'task';
        $feature->save();

        // Bills Section
        $feature = new \App\Feature();
        $feature->title = 'Estimates';
        $feature->description = '<span style="color: rgb(68, 68, 68); font-family: Lato, sans-serif; font-size: 16px; text-align: center;">Create estimates how much project can cost and send to your clients.</span>';
        $feature->icon = 'fas fa-calculator';
        $feature->type = 'bills';
        $feature->save();

        $feature = new \App\Feature();
        $feature->title = 'Invoices';
        $feature->description = '<span style="color: rgb(68, 68, 68); font-family: Lato, sans-serif; font-size: 16px; text-align: center;">Simple and professional invoices can be download in form of PDF.</span>';
        $feature->icon = 'far fa-file-alt';
        $feature->type = 'bills';
        $feature->save();

        $feature = new \App\Feature();
        $feature->title = 'Payments';
        $feature->description = '<span style="color: rgb(68, 68, 68); font-family: Lato, sans-serif; font-size: 16px; text-align: center;">Track payments done by clients in the payment section.</span>';
        $feature->icon = 'fas fa-money-bill-alt';
        $feature->type = 'bills';
        $feature->save();

        // Teamates Section
        $feature = new \App\Feature();
        $feature->title = 'Tickets';
        $feature->description = '<span style="color: rgb(68, 68, 68); font-family: Lato, sans-serif; font-size: 16px; text-align: center;">When someone is facing a problem, they can raise a ticket for their problems. Admin can assign the tickets to respective department agents.</span>';
        $feature->icon = 'fas fa-ticket-alt';
        $feature->type = 'team';
        $feature->save();

        $feature = new \App\Feature();
        $feature->title = 'Leaves';
        $feature->description = '<span style="color: rgb(68, 68, 68); font-family: Lato, sans-serif; font-size: 16px; text-align: center;">Employees can apply for the multiple leaves from their panel. Admin can approve or reject the leave applications.</span>';
        $feature->icon = 'fas fa-ban';
        $feature->type = 'team';
        $feature->save();

        $feature = new \App\Feature();
        $feature->title = 'Attendance';
        $feature->description = '<span style="color: rgb(68, 68, 68); font-family: Lato, sans-serif; font-size: 16px; text-align: center;">Attendance module allows employees to clock-in and clock-out, right from their dashboard. Admin can track the attendance of the team.</span>';
        $feature->icon = 'far fa-check-circle';
        $feature->type = 'team';
        $feature->save();


        // Application Section
        $feature = new \App\Feature();
        $feature->title = 'Github';
        $feature->type = 'apps';
        $feature->save();

        $feature = new \App\Feature();
        $feature->title = 'OneSignal';
        $feature->type = 'apps';
        $feature->save();

        $feature = new \App\Feature();
        $feature->title = 'Mailchimp';
        $feature->type = 'apps';
        $feature->save();

        $feature = new \App\Feature();
        $feature->title = 'Dropbox';
        $feature->type = 'apps';
        $feature->save();

        $feature = new \App\Feature();
        $feature->title = 'Slack';
        $feature->type = 'apps';
        $feature->save();

        $feature = new \App\Feature();
        $feature->title = 'Paypal';
        $feature->type = 'apps';
        $feature->save();

        // Front Client
        $client = new \App\FrontClients();
        $client->title = 'Client 1';
        $client->save();

        $client = new \App\FrontClients();
        $client->title = 'Client 2';
        $client->save();

        $client = new \App\FrontClients();
        $client->title = 'Client 3';
        $client->save();

        $client = new \App\FrontClients();
        $client->title = 'Client 4';
        $client->save();

        // Testimonial
        $client = new \App\Testimonials();
        $client->name    = 'theon salvatore';
        $client->comment = 'Lorem ipsum dolor sit detudzdae amet, rcquisc adipiscing elit.
                            Aenean amet socada commodo sit.';
        $client->rating = 5;
        $client->save();

        $client = new \App\Testimonials();
        $client->name    = 'jenna gilbert';
        $client->comment = 'Lorem ipsum dolor sit detudzdae amet, rcquisc adipiscing elit.
                            Aenean amet socada commodo sit.';
        $client->rating = 4;
        $client->save();

        $client = new \App\Testimonials();
        $client->name    = 'Redh gilbert';
        $client->comment = 'Lorem ipsum dolor sit detudzdae amet, rcquisc adipiscing elit.
                            Aenean amet socada commodo sit.';
        $client->rating = 3;
        $client->save();

        $client = new \App\Testimonials();
        $client->name    = 'angela whatson';
        $client->comment = 'Lorem ipsum dolor sit detudzdae amet, rcquisc adipiscing elit.
                            Aenean amet socada commodo sit.';
        $client->rating = 4;
        $client->save();

        $client = new \App\Testimonials();
        $client->name    = 'angela whatson';
        $client->comment = 'Lorem ipsum dolor sit detudzdae amet, rcquisc adipiscing elit.
                            Aenean amet socada commodo sit.';
        $client->rating = 2;
        $client->save();

        //Front FAQ
        $client = new \App\FrontFaq();
        $client->question    = 'Can i see demo?';
        $client->answer = '<span style="color: rgb(68, 68, 68); font-family: Lato, sans-serif; font-size: 16px;">Yes, definitely. We would be happy to demonstrate you Worksuite through a web conference at your convenience. Please submit a query on our contact us page or drop a mail to our mail id worksuite@froiden.com.</span>';
        $client->save();

        $client = new \App\FrontFaq();
        $client->question    = 'How can i update app?';
        $client->answer = '<span style="color: rgb(68, 68, 68); font-family: Lato, sans-serif; font-size: 16px;">Yes, definitely. We would be happy to demonstrate you Worksuite through a web conference at your convenience. Please submit a query on our contact us page or drop a mail to our mail id worksuite@froiden.com.</span>';
        $client->save();

    }
}
