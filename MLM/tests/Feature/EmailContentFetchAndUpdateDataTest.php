<?php


namespace MLM\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MLM\Models\EmailContentSetting;
use MLM\tests\MLMTest;

class EmailContentFetchAndUpdateDataTest extends MLMTest
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function admin_can_fetch_email_contents()
    {
        $this->withHeaders($this->getHeaders());
        $resp = $this->get(route('admin.email-contents.list'));
        $resp->assertOk();
    }

    /**
     * @test
     */
    public function admin_can_update_email_content()
    {
        $this->withHeaders($this->getHeaders());
        $email_content = EmailContentSetting::query()->first();
        $resp = $this->patch(route('admin.email-contents.update'),[
            'key' => $email_content->key,
            'is_active' => $email_content->is_active,
            'subject' => 'Test change subject',
            'body' => $email_content->body
        ]);
        $resp->assertOk();
        $this->assertDatabaseHas('email_content_settings',[
            'key' => $email_content->key,
            'is_active' => $email_content->is_active,
            'subject' => 'Test change subject'
        ]);
    }


}
