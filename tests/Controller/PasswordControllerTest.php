<?php
/**
 * @author Jörn Friedrich Dreyer <jfd@butonic.de>
 *
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

namespace OCA\PasswordPolicy\Tests\Controller;

use OCA\PasswordPolicy\Controller\PasswordController;
use OCA\PasswordPolicy\Rules\PolicyException;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IRequest;
use OCP\ISession;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\IUserManager;
use OCP\IUserSession;
use Test\TestCase;

class PasswordControllerTest extends TestCase {

	/** @var IRequest | \PHPUnit_Framework_MockObject_MockObject */
	protected $request;
	/** @var IUserSession | \PHPUnit_Framework_MockObject_MockObject */
	protected $userSession;
	/** @var IUserManager | \PHPUnit_Framework_MockObject_MockObject */
	protected $userManager;
	/** @var IConfig | \PHPUnit_Framework_MockObject_MockObject */
	protected $config;
	/** @var ISession | \PHPUnit_Framework_MockObject_MockObject */
	protected $session;
	/** @var IURLGenerator | \PHPUnit_Framework_MockObject_MockObject */
	protected $urlGenerator;
	/** @var IL10N | \PHPUnit_Framework_MockObject_MockObject */
	protected $l10n;
	/** @var PasswordController | \PHPUnit_Framework_MockObject_MockObject */
	private $c;

	public function setUp() {
		parent::setUp();

		$this->request = $this->createMock(IRequest::class);
		$this->userSession = $this->createMock(IUserSession::class);
		$this->userManager = $this->createMock(IUserManager::class);
		$this->config = $this->createMock(IConfig::class);
		$this->session = $this->createMock(ISession::class);
		$this->urlGenerator = $this->createMock(IURLGenerator::class);
		$this->l10n = $this->createMock(IL10N::class);
		$this->l10n
			->method('t')
			->will($this->returnCallback(function ($text, $parameters = []) {
				return \vsprintf($text, $parameters);
			}));
		$this->c = $this->getMockBuilder(PasswordController::class)
			->setConstructorArgs([
				'password_policy',
				$this->request,
				$this->userSession,
				$this->userManager,
				$this->config,
				$this->session,
				$this->urlGenerator,
				$this->l10n
			])
			->setMethods(['createPasswordTemplateResponse'])
			->getMock();
	}

	public function testCreatePasswordTemplateResponse() {
		$this->c = new PasswordController(
				'password_policy',
				$this->request,
				$this->userSession,
				$this->userManager,
				$this->config,
				$this->session,
				$this->urlGenerator,
				$this->l10n
			);

		$this->config->expects($this->exactly(2))
			->method('getUserValue')
			->willReturn('1');

		$iUser = $this->createMock(IUser::class);
		$iUser->expects($this->any())
			->method('getUID')
			->willReturn('testuid');
		$this->userSession->expects($this->exactly(2))
			->method('getUser')
			->willReturn($iUser);
		self::assertInstanceOf(
			TemplateResponse::class,
			self::invokePrivate(
			$this->c, 'createPasswordTemplateResponse',
			['redirect/target', 'Error message']
		));
		$this->assertInstanceOf(TemplateResponse::class,
			$this->invokePrivate($this->c, 'createPasswordTemplateResponse', ['']));
	}

	public function testShowWeb() {
		$this->request
			->expects($this->once())
			->method('getHeader')
			->willReturn(null);

		$redirect_url = 'redirect/target';
		$this->c->expects($this->once())
			->method('createPasswordTemplateResponse')
			->with($redirect_url, null);

		$this->c->show('redirect/target');
	}

	public function testShowOCP() {
		$this->request
			->expects($this->once())
			->method('getHeader')
			->willReturn('true');

		self::assertInstanceOf(
			JSONResponse::class,
			$this->c->show('redirect/target')
		);
	}

	public function testUpdatePasswordsMismatch() {
		$redirect_url = 'redirect/target';
		$this->c->expects($this->once())
			->method('createPasswordTemplateResponse')
			->with($redirect_url, 'Password confirmation does not match the password.');

		$iUser = $this->createMock(IUser::class);
		$iUser->expects($this->once())
			->method('getUID')
			->willReturn('foo');
		$this->userSession
			->expects($this->once())
			->method('getUser')
			->willReturn($iUser);

		$this->userManager->expects($this->once())
			->method('checkPassword')
			->willReturn(true);

		$this->session
			->expects($this->never())
			->method('remove');
		$this->config
			->expects($this->never())
			->method('deleteUserValue');

		$this->c->update(null, 'newsecret', 'different', $redirect_url);
	}

	public function testUpdatePasswordsMatchesCurrent() {
		$redirect_url = 'redirect/target';
		$this->c->expects($this->once())
			->method('createPasswordTemplateResponse')
			->with($redirect_url, 'Password must be different than the old password.');

		$iUser = $this->createMock(IUser::class);
		$iUser->expects($this->once())
			->method('getUID')
			->willReturn('foo');

		$this->userSession
			->expects($this->once())
			->method('getUser')
			->willReturn($iUser);

		$this->userManager->expects($this->once())
			->method('checkPassword')
			->willReturn(true);

		$this->session
			->expects($this->never())
			->method('remove');
		$this->config
			->expects($this->never())
			->method('deleteUserValue');

		$this->c->update('oldsecret', 'oldsecret', 'oldsecret', $redirect_url);
	}

	public function testUpdateWrongPassword() {
		$user = $this->createMock(IUser::class);
		$user
			->method('getUID')
			->willReturn('testuid');

		$this->userSession
			->expects($this->once())
			->method('getUser')
			->willReturn($user);

		$this->userManager
			->expects($this->once())
			->method('checkPassword')
			->willReturn(false);

		$redirect_url = 'redirect/target';
		$this->c->expects($this->once())
			->method('createPasswordTemplateResponse')
			->with($redirect_url, 'Old password is wrong.');

		$user
			->expects($this->never())
			->method('setPassword');

		$this->session
			->expects($this->never())
			->method('remove');
		$this->config
			->expects($this->never())
			->method('deleteUserValue');

		$this->c->update(null, 'newsecret', 'newsecret', $redirect_url);
	}

	public function testUpdateSetPasswordException() {
		$user = $this->createMock(IUser::class);
		$user
			->method('getUID')
			->willReturn('testuid');

		$this->userSession
			->expects($this->once())
			->method('getUser')
			->willReturn($user);

		$this->userManager
			->expects($this->once())
			->method('checkPassword')
			->willReturn(true);

		$user
			->expects($this->once())
			->method('setPassword')
			->willThrowException(new PolicyException('A password policy check failed.'));

		$redirect_url = 'redirect/target';
		$this->c->expects($this->once())
			->method('createPasswordTemplateResponse')
			->with($redirect_url, 'A password policy check failed.');

		$this->session
			->expects($this->never())
			->method('remove');
		$this->config
			->expects($this->never())
			->method('deleteUserValue');

		$this->c->update(null, 'newsecret', 'newsecret', $redirect_url);
	}

	/**
	 * @expectedException \UnexpectedValueException
	 * @expectedExceptionMessage Unable to update the user's password.
	 */
	public function testUpdateSetPasswordFails() {
		$user = $this->createMock(IUser::class);
		$user
			->method('getUID')
			->willReturn('testuid');

		$this->userSession
			->expects($this->once())
			->method('getUser')
			->willReturn($user);

		$this->userManager
			->expects($this->once())
			->method('checkPassword')
			->willReturn(true);

		$user
			->expects($this->once())
			->method('setPassword')
			->willReturn(false);

		$redirect_url = 'redirect/target';
		$this->c->expects($this->never())
			->method('createPasswordTemplateResponse');

		$this->session
			->expects($this->never())
			->method('remove');
		$this->config
			->expects($this->never())
			->method('deleteUserValue');

		$this->c->update(null, 'newsecret', 'newsecret', $redirect_url);
	}

	public function testUpdateSuccess() {
		$user = $this->createMock(IUser::class);
		$user
			->method('getUID')
			->willReturn('testuid');

		$this->userSession
			->expects($this->once())
			->method('getUser')
			->willReturn($user);

		$this->userManager
			->expects($this->once())
			->method('checkPassword')
			->willReturn(true);

		$user
			->expects($this->once())
			->method('setPassword')
			->willReturn(true);

		$redirect_url = 'redirect/target';
		$this->c->expects($this->never())
			->method('createPasswordTemplateResponse');

		$this->session
			->expects($this->once())
			->method('remove')
			->with('password_policy.forcePasswordChange');
		$this->config
			->expects($this->any())
			->method('deleteUserValue')
			->withConsecutive(
				['testuid', 'password_policy', 'forcePasswordChange'],
				['testuid', 'password_policy', 'firstLoginPasswordChange']
			);
		$this->config
			->expects($this->once())
			->method('getUserValue')
			->willReturn(true);

		$redirectResponse = $this->c->update(null, 'newsecret', 'newsecret', $redirect_url);
		self::assertInstanceOf(
			RedirectResponse::class,
			$redirectResponse
		);
		self::assertSame($redirect_url, $redirectResponse->getRedirectURL());
	}
}
