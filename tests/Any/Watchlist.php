<?php

class Any_Watchlist extends AbstractAction {
	public function setUp() {

		parent::setUp();

		$this->_users = array
		(
		'admin'          => TRUE,
		'anonymous'      => FALSE,
		'logged_in'      => TRUE,
		'splunk_preview' => TRUE,
		'storm_preview'  => TRUE,
		'employee'       => TRUE,
		'splunk_docteam' => TRUE,
		'storm_docteam'  => TRUE,
		'docteam'        => TRUE
		);

	}

	public function testWatchlist() {

		$editTypes = array('regular', 'raw');

		foreach ($this->_users as $user => $allowed) {

			if ($user != 'anonymous') $this->_login($user);

			if ($allowed) {
				$this->open("/Main_Page");
				$this->select("docsManualSelect", "label=Splunk User Manual");
				$this->waitForPageToLoad("30000");
				$this->click("link=Watch");
				for ($second = 0; ; $second++) {
					if ($second >= 60) $this->fail("timeout");
					try {
						if ($this->isElementPresent("link=watchlist")) break;
					} catch (Exception $e) {}
					sleep(1);
				}
				$this->click("link=watchlist");
				$this->waitForPageToLoad("30000");
				$this->click("link=all");
			    $this->waitForPageToLoad("30000");
				// Watch topic succeeded
				$this->assertTrue($this->isElementPresent("link=exact:Documentation:Splunk:User:SplunkOverview:1.0"));
				foreach ($editTypes as $editType) {
					if ($editType == 'regular') {

						for ($second = 0; ; $second++) {
							if ($second >= 60) $this->fail("timeout");
							try {
								// Edit watchlist link is there
								if ($this->isElementPresent("link=View and edit watchlist")) break;
							} catch (Exception $e) {}
							sleep(1);
						}
						$this->click("link=View and edit watchlist");
						$this->waitForPageToLoad("30000");
						$this->click("titles[]");
						$this->click("css=input[type=submit]");
						$this->waitForPageToLoad("30000");
						// Topic edit/removal succeeded
						$this->assertTrue($this->isTextPresent("1 title was removed from your watchlist:"));

					} else if ($editType == 'raw') {

						for ($second = 0; ; $second++) {
							if ($second >= 60) $this->fail("timeout");
							try {
								// Edit raw watchlist link is there
								if ($this->isElementPresent("link=Edit raw watchlist")) break;
							} catch (Exception $e) {}
							sleep(1);
						}
						$this->click("link=Edit raw watchlist");
					    $this->waitForPageToLoad("30000");
						$this->type("titles", "Documentation:Splunk:User:SplunkOverview:1.0\nDocumentation:Splunk:User:SplunkOverview:2.0\nSplunk:User:SplunkOverview:1.0");
						$this->click("css=input[type=submit]");
						$this->waitForPageToLoad("30000");
						for ($second = 0; ; $second++) {
							if ($second >= 60) $this->fail("timeout");
							try {
								// Topics were added via raw watchlist edit tool
								if ($this->isTextPresent("titles were added") || $this->isTextPresent("title was added")) break;
							} catch (Exception $e) {}
							sleep(1);
						}

						$this->type("titles", "");
						$this->click("css=input[type=submit]");
						$this->waitForPageToLoad("30000");
						// Topics were removed via raw watchlist edit tool
						$this->assertTrue($this->isTextPresent("titles were removed") || $this->isTextPresent("title was removed"));

					}
				}
			} else {
				$this->open("/Main_Page");
				$this->select("docsManualSelect", "label=Splunk User Manual");
				$this->waitForPageToLoad("30000");
				$this->assertFalse($this->isElementPresent("link=Watch"));
				$this->open("/index.php?title=Documentation:Splunk:Installation:WhatsinSplunkInstallationManual:1.0&action=watch");
				$this->assertTrue($this->isTextPresent("You must be logged in to modify your watchlist."));
			}

			if ($user != 'anonymous') $this->_logout();
		}
	}
}

?>