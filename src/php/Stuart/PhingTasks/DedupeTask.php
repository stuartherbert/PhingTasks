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
 * Remove duplicate files from a folder
 *
 * Useful for cleaning up after we've used PEAR / Pyrus to build
 * the vendor folder
 */
class DedupeTask extends Task
{
	protected $src;
	protected $from;

	public function removeEmptyFolders($root)
	{
		if (!is_dir($root)) {
			return;
		}

		$objects = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($root),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach($objects as $fullname => $object)
		{
			// we don't want every file
			$basename = basename($fullname);

			// skip over '.' and '..'
			if ($basename == '.' || $basename == '..') {
				continue;
			}

			// skip over files
			if (!is_dir($fullname)) {
				continue;
			}

			// is this directory empty?
			$contents = glob($fullname . DIRECTORY_SEPARATOR . '*');
			$contents = array_merge($contents, glob($fullname . DIRECTORY_SEPARATOR . '.*'));

			if (count($contents) == 2 && basename($contents[0]) == '.' && basename($contents[1]) == '..') {
				// we think this is empty
				$this->log("Removing empty directory '{$fullname}'.", Project::MSG_DEBUG);

				rmdir($fullname);
			}
		}

		// all done
	}

	public function getFiles($root)
	{
		$return = array();

		$objects = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($root),
			RecursiveIteratorIterator::SELF_FIRST
		);

		foreach($objects as $fullname => $object)
		{
			// we don't want every file
			$basename = basename($fullname);

			// skip over '.' and '..'
			if ($basename == '.' || $basename == '..') {
				continue;
			}

			// if we get here, we want this file
			$name = str_replace($root, '', $fullname);
    		$return[] = $name;
		}

		// all done
		return $return;
	}

	public function setSrc($src)
	{
		$this->src = $src;
	}

	public function setFrom($from)
	{
		$this->from = $from;
	}

	public function main()
	{
		if (!$this->src)
		{
			throw new BuildException("Attribute src is required.", $this->getLocation());
		}

		if (!$this->from)
		{
			throw new BuildException("Attribute from is required.", $this->getLocation());
		}

		$this->log("Running Dedupe to remove files in '{$this->src}' from '{$this->from}'.", Project::MSG_DEBUG);

		// get the list of files to dedupe
		$files = $this->getFiles($this->src);

		// let's clean things up
		foreach ($files as $subpath) {
			if (is_file($this->from . $subpath)) {
				unlink($this->from . $subpath);
			}
		}

		// finally, let's remove all empty directories
		$this->removeEmptyFolders($this->from);
	}
}