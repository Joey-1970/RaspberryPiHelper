<?
    // Klassendefinition
    class RaspberryPiHelper extends IPSModule 
    { 
	// Überschreibt die interne IPS_Create($id) Funktion
        public function Create() 
        {
            	// Diese Zeile nicht löschen.
            	parent::Create();
 	    	$this->RegisterPropertyBoolean("Open", false);
	
        }
 	
	public function GetConfigurationForm() 
	{ 
		$arrayStatus = array(); 
		$arrayStatus[] = array("code" => 101, "icon" => "inactive", "caption" => "Instanz wird erstellt"); 
		$arrayStatus[] = array("code" => 102, "icon" => "active", "caption" => "Instanz ist aktiv");
		$arrayStatus[] = array("code" => 104, "icon" => "inactive", "caption" => "Instanz ist inaktiv");
				
		$arrayElements = array(); 
		$arrayElements[] = array("name" => "Open", "type" => "CheckBox",  "caption" => "Aktiv"); 
 		
		$arrayActions = array();
		$arrayActions[] = array("type" => "Label", "label" => "Test Center"); 
		$arrayActions[] = array("type" => "TestCenter", "name" => "TestCenter");
		
 		return JSON_encode(array("status" => $arrayStatus, "elements" => $arrayElements, "actions" => $arrayActions)); 		 
 	}       
	   
        // Überschreibt die intere IPS_ApplyChanges($id) Funktion
        public function ApplyChanges() 
        {
            	// Diese Zeile nicht löschen
            	parent::ApplyChanges();
		
		// Profil anlegen
		
	
		// Statusvariablen
		$this->RegisterVariableBoolean("VPN_Connect", "VPN Connect", "~Switch", 10);
		$this->EnableAction("VPN_Connect");
		
		$this->RegisterVariableBoolean("VPN_Disconnect", "VPN Disconnect", "~Switch", 20);
		$this->EnableAction("VPN_Disconnect");
		
		$this->RegisterVariableBoolean("VPN_Status", "VPN Status", "~Switch", 25);
		$this->EnableAction("VPN_Status");
		
		$this->RegisterVariableBoolean("Reboot", "Reboot", "~Switch", 30);
		$this->EnableAction("Reboot");
		
		$this->RegisterVariableBoolean("WLAN_Information", "WLAN Information", "~Switch", 40);
		$this->EnableAction("WLAN_Information");
		
		$this->RegisterVariableString("Result_Text", " Ergebnis Text", "~TextBox", 300);
		
		$this->RegisterVariableInteger("Result_Code", "Ergebnis Code", "", 310);
		
		If ($this->HasActiveParent() == true) {	
			If ($this->ReadPropertyBoolean("Open") == true) {
				If ($this->GetStatus() <> 102) {
					$this->SetStatus(102);
				}
			}
			else {
				If ($this->GetStatus() <> 104) {
					$this->SetStatus(104);
				}
			}
		}
	}
	
	public function RequestAction($Ident, $Value) 
	{
		switch($Ident) {
		case "VPN_Connect":
			$this->VPN_Connect();
			break;
		case "VPN_Disconnect":
			$this->VPN_Disconnect();
			break;
		case "VPN_Status":
			$this->VPN_Status();
			break;
		case "Reboot":
			$this->Reboot();
			break;
		case "WLAN_Information":
			$this->WLAN_Information();
			break;
		
		default:
		    throw new Exception("Invalid Ident");
		}
	}
	    
	// Beginn der Funktionen
	public function VPN_Connect()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("VPN_Connect", "Ausfuehrung", 0);
			$this->SetValue("VPN_Connect", true);
			exec("sudo wg-quick up wg0", $Lines, $Result_Code);
			$this->ShowOutput(serialize($Lines), $Result_Code);
			$this->SetValue("VPN_Connect", false);
		}
	}
	    
	public function VPN_Disconnect()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("VPN_Disconnect", "Ausfuehrung", 0);
			$this->SetValue("VPN_Disconnect", true);
			exec("sudo wg-quick down wg0", $Lines, $Result_Code);
			$this->ShowOutput(serialize($Lines), $Result_Code);
			$this->SetValue("VPN_Disconnect", false);
		}
	}
	    
	public function VPN_Status()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("VPN_Status", "Ausfuehrung", 0);
			$this->SetValue("VPN_Status", true);
			exec("sudo systemctl status wg-quick@wg0", $Lines, $Result_Code);
			$this->ShowOutput(serialize($Lines), $Result_Code);
			$this->SetValue("VPN_Status", false);
		}
	}
	    
	public function Reboot()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("Reboot", "Ausfuehrung", 0);
			$this->SetValue("Reboot", true);
			exec("sudo reboot", $Lines, $Result_Code);
			$this->ShowOutput(serialize($Lines), $Result_Code);
			$this->SetValue("Reboot", false);
		}
	}
	    
	public function WLAN_Information()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("WLAN_Information", "Ausfuehrung", 0);
			$this->SetValue("WLAN_Information", true);
			exec("iwconfig", $Lines, $Result_Code);
			$this->ShowOutput(serialize($Lines), $Result_Code);
			$this->SetValue("WLAN_Information", false);
		}
	}
	    
	private function ShowOutput(String $Lines, Int $Result_Code)
	{
		$ResultText = "";
		foreach (unserialize($Lines) as $key => $value) {
			$ResultText = $ResultText."$value\n";
		}
		$this->SetValue("Result_Text", $ResultText);
		$this->SetValue("Result_Code", $Result_Code);
	}
	    
	private function RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize)
	{
	        if (!IPS_VariableProfileExists($Name))
	        {
	            IPS_CreateVariableProfile($Name, 1);
	        }
	        else
	        {
	            $profile = IPS_GetVariableProfile($Name);
	            if ($profile['ProfileType'] != 1)
	                throw new Exception("Variable profile type does not match for profile " . $Name);
	        }
	        IPS_SetVariableProfileIcon($Name, $Icon);
	        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
	        IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);    
	}    
}
?>
