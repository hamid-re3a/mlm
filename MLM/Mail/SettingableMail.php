<?php


namespace MLM\Mail;

use MLM\Models\EmailContentSetting;

interface SettingableMail
{
    public function getSetting(): array ;
}
