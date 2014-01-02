<?php

/**
 * Copyright (c) 2011-present Stuart Herbert.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     Stuart
 * @subpackage  PhingTasks
 * @author      Stuart Herbert <stuart@stuartherbert.com>
 * @copyright   2011-present Stuart Herbert www.stuartherbert.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://code.stuartherbert.com/php/PhingTasks
 * @version     @@PACKAGE_VERSION@@
 */

require_once "phing/Task.php";

/**
 * Attempts to call a target in the loaded Phing build.xml just like
 * PhingCallTarget, but does not error if the target does not exist.
 *
 * NOTE: This class conforms to Phing's class-naming scheme, which is
 * not PSR-0 compatible.
 */
class PhingCallIfExistsTask extends Task
{
	protected $callee;
	protected $targetname;

	public function setTarget($target)
	{
		$this->targetname = $target;
	}

	public function init()
	{
		$this->callee = $this->project->createTask('phing');
		$this->callee->setOwningTarget($this->getOwningTarget());
		$this->callee->setTaskName($this->getTaskName());
		$this->callee->setHaltOnFailure(true);
		$this->callee->setLocation($this->getLocation());
		$this->callee->init();
	}

	public function main()
	{
		$this->log("Running PhingCallIfExists for target '{$this->targetname}'.", Project::MSG_DEBUG);

		if( ! $this->callee)
		{
			$this->init();
		}

		if( ! $this->targetname)
		{
			throw new BuildException("Attribute target is required.", $this->getLocation());
		}

		$targets = $this->project->getTargets();

		if( ! isset($targets[$this->targetname]))
		{
			$this->log("Aborting PhingCallIfExists for target '{$this->targetname}', target does not exist.", Project::MSG_DEBUG);

			return;
		}

		$this->callee->setPhingfile($this->project->getProperty("phing.file"));
		$this->callee->setTarget($this->targetname);
		$this->callee->setInheritAll(false);
		$this->callee->setInheritRefs(false);
		$this->callee->main();
	}
}