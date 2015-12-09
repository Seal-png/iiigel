<?php namespace Iiigel\Controller;

class Group extends \Iiigel\Controller\StaticPage {
	const DEFAULT_ACTION = 'show';
    
    /**
     * Display the profile of a user.
     * 
     * @param string $_sHashId hashed string represents the user
     */
    public function show($_sHashId = NULL) {
    	if ($_sHashId == NULL) {
	    	throw new \Exception(_('error.permission'));
    	} else {
    		$oGroup = new \Iiigel\Model\Group($_sHashId);
			$oGroup->load();
    	}
    	
    	$this->oView->aGroup = $oGroup->getCompleteEntry();
    	
		$oSingle = new \Iiigel\Model\GroupAffiliation();
		
		$aNotLeaders = array();
		$aNotMembers = array();
		
		$aLeaders = array();
		$aMembers = array();
		$aModules = array();
		
		$oResult = $oSingle->getList($_sHashId, $oSingle::MODE_POSSIBLE);
		
		while(($aRow = $GLOBALS['oDb']->get($oResult))) {
            $oTemp = new \Iiigel\Model\User($aRow);
           	$aNotMembers[] = $oTemp->getCompleteEntry();
        }
        
        $oResult = $oSingle->getList($_sHashId, $oSingle::MODE_POSSIBLE);
		
		while(($aRow = $GLOBALS['oDb']->get($oResult))) {
            $oTemp = new \Iiigel\Model\User($aRow);
           	$aNotLeaders[] = $oTemp->getCompleteEntry();
        }
		
		$oResult = $oSingle->getList($_sHashId, $oSingle::MODE_MEMBER);
		
		while(($aRow = $GLOBALS['oDb']->get($oResult))) {
			$oTempModule = new \Iiigel\Model\Module(intval($aRow['nIdModule']));
			
			unset($aRow['nIdModule']);
			
            $oTemp = new \Iiigel\Model\User($aRow);
            
            for ($i = count($aNotMembers) - 1; $i >= 0; $i--) {
            	if ($aNotMembers[$i]['sHashId'] === $oTemp->sHashId) {
            		unset($aNotMembers[$i]);
            	}
            }
            
            $aEntry = $oTemp->getCompleteEntry();
            
            $aEntry['sModuleImage'] = $oTempModule->sImage;
            $aEntry['nModuleProgress'] = $oTempModule->getProgress($oTemp->nId);
            
           	$aMembers[] = $aEntry;
        }
		
		$oResult = $oSingle->getList($_sHashId, $oSingle::MODE_LEADER);
		
		while(($aRow = $GLOBALS['oDb']->get($oResult))) {
            $oTemp = new \Iiigel\Model\User($aRow);
            
            for ($i = count($aNotLeaders) - 1; $i >= 0; $i--) {
            	if ($aNotLeaders[$i]['sHashId'] === $oTemp->sHashId) {
            		unset($aNotLeaders[$i]);
            	}
            }
            
           	$aLeaders[] = $oTemp->getCompleteEntry();
        }
		
		$oResult = $oSingle->getList($_sHashId, $oSingle::MODE_MODULE);
		
		while(($aRow = $GLOBALS['oDb']->get($oResult))) {
            $oTemp = new \Iiigel\Model\Module($aRow);
			$aModules[] = $oTemp->getCompleteEntry();
        }
		
		$this->oView->aGroupLeaders = $aLeaders;
    	$this->oView->aGroupMembers = $aMembers;
		$this->oView->aGroupModules = $aModules;
		
		$this->oView->aNotGroupLeaders = $aNotLeaders;
    	$this->oView->aNotGroupMembers = $aNotMembers;
		
		$this->oView->bGroupEdit = false;
    	
    	$this->loadFile('group');
    }

}

?>
