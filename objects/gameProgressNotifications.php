<?php
/*
    Copyright (C) 2004-2010 Kestas J. Kuliukas

  This file is part of webDiplomacy.

    webDiplomacy is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    webDiplomacy is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with webDiplomacy.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once(l_r('objects/mailer.php'));
require_once(l_r('objects/user.php'));

class GameProgressNotifications {
  
  static const TYPES = (
    'PROGRESSED' => 'PROGRESSED',
    'FINISHED' => 'FINISHED'
  );

  public function __construct($game) {
    $this->Game = $game;
    $this->Mailer = new Mailer();
  }

  public function send($type, $members) {
    if (!array_key_exists($type, self::TYPES)) {
      // The caller messed up. Probably not worth erroring out.
      return;
    }

    foreach ($members as $member) {
      // TODO Check any user preferences here before notifying
      $user = new User($member->userId);

      $this->sendEmail($type, $member, $user);
      $this->sendPushNotification($type, $member, $user);
    }
  }

  function sendEmail($type, $member, $user) {
    $subject = null;
    $message = null;

    switch($type) {
      case 'PROGRESSED'
        $gameName =  $this->Game->name;
        $gameUrl = 'https://webdiplomacy.net/board.php?gameID='.$this->Game->id.'#gamePanel';

        $subject = l_t('webDiplomacy game %s has progressed', $gameName);
        $message = l_t('Hello!').'<br><br>'.l_t('Game ').'<a href="{$gameUrl}">{$gameName}</>'.l_t(' has progressed to the next phase. Visit the game page to plan your next orders.').'<br><br>'.l_t('Good luck!');
        break;
      case 'FINISHED'
        break;
    }

    if ($subject && $message) {
      $Mailer->Send(array($email=>$user->email), $subject, $message);
    }
  }

  function sendPushNotification($type, $member) {
    // Maybe one day
  }
}
