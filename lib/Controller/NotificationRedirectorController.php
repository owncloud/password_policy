<?php
/**
 * @author Juan Pablo Villafáñez <jvillafanez@solidgear.es>
 * @copyright Copyright (c) 2018, ownCloud GmbH
 * @license GPL-2.0
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\PasswordPolicy\Controller;

use OC\OCS\Result;
use OCP\AppFramework\OCSController;
use OCP\Notification\IManager;
use OCP\IUserSession;
use OCP\IURLGenerator;
use OCP\IRequest;

class NotificationRedirectorController extends OCSController {
	/** @var IManager */
	private $notificationManager;

	/** @var IUserSession */
	private $session;

	/** @var IURLGenerator */
	private $urlGenerator;

	public function __construct(
		$appName,
		IRequest $request,
		IManager $notificationManager,
		IUserSession $session,
		IURLGenerator $urlGenerator
	) {
		parent::__construct($appName, $request);
		$this->notificationManager = $notificationManager;
		$this->session = $session;
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param int $id
	 * @return Result
	 */
	public function markAndRedirectAboutToExpire($id) {
		$currentUser = $this->session->getUser();
		if ($currentUser === null) {
			return new Result(null, Http::STATUS_NO_CONTENT);
		}
		$userid = $currentUser->getUID();

		$notification = $this->notificationManager->createNotification();
		$notification->setApp('password_policy')
			->setUser($userid)
			->setObject('about_to_expire', $id);

		$this->notificationManager->markProcessed($notification);

		$targetRedirection = $this->urlGenerator->linkToRouteAbsolute(
			'settings.SettingsPage.getPersonal',
			['sectionid' => 'general']
		);

		return new Result(['redirectTo' => $targetRedirection]);
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param int $id
	 * @return Result
	 */
	public function markAndRedirectExpired($id) {
		$currentUser = $this->session->getUser();
		if ($currentUser === null) {
			return new Result(null, Http::STATUS_NO_CONTENT);
		}
		$userid = $currentUser->getUID();

		$notification = $this->notificationManager->createNotification();
		$notification->setApp('password_policy')
			->setUser($userid)
			->setObject('expired', $id);

		$this->notificationManager->markProcessed($notification);

		$targetRedirection = $this->urlGenerator->linkToRouteAbsolute(
			'settings.SettingsPage.getPersonal',
			['sectionid' => 'general']
		);

		return new Result(['redirectTo' => $targetRedirection]);
	}
}
