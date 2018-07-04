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

namespace OCA\PasswordPolicy\Command;

use OCP\IConfig;
use OCP\IUserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ExpirePassword extends Command {
	/** @var \OCP\IConfig */
	private $config;

	/** @var \OCP\IUserManager */
	protected $userManager;

	/**
	 * @param IConfig $config
	 * @param IUserManager $userManager
	 */
	public function __construct(IConfig $config, IUserManager $userManager) {
		parent::__construct();
		$this->config = $config;
		$this->userManager = $userManager;
	}

	protected function configure() {
		$this
			->setName('user:expire-password')
			->setDescription('Expire a user\'s password')
			->addArgument(
					'uid',
					InputArgument::REQUIRED,
					'The user\'s ownCloud uid (username).'
				     )
			->addArgument(
					'expiredate',
					InputArgument::OPTIONAL,
					'The date and time when a password expires, e.g. "2019-01-01 14:00:00 CET".',
					'-1 days'		// base.php sets timezone to utc, so
										// make sure date is in the past
				     )
		;
	}

	/**
	 * sets a user value as a flag in the user config that will be checked by
	 * the AccountModule on login.
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 * @throws \OCP\PreConditionNotMetException
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$uid = $input->getArgument('uid');

		$exists = $this->userManager->userExists($uid);
		if($exists === false) {
			$output->writeln("<error>Unknown user: $uid</error>");
			/**
			 * return EX_NOUSER from /usr/include/sysexits.h
			 * @see http://tldp.org/LDP/abs/html/exitcodes.html#FTN.AEN23647
			 */
			return 67;
		}

		$date = new \DateTime($input->getArgument('expiredate'));
		$date->setTimezone(new \DateTimeZone('UTC'));
		$value = $date->format('Y-m-d\TH:i:s\Z'); // ISO8601 with Zulu = UTC

		$this->config->setUserValue(
			$uid,
			'password_policy',
			'forcePasswordChange',
			$value
		);

		// show expire date if it was given
		if ($input->hasArgument('expiredate')) {
			$output->writeln("The password for $uid is set to expire on ". $date->format('Y-m-d H:i:s T').'.');
		}
		return 0;
	}
}
