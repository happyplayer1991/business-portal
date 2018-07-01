<?php
class Ves_Brand_Helper_Recaptcha extends Mage_Core_Helper_Abstract {
	
	/**
	 * @var string $privateKey
	 *
	 * @access protected
	 */
	protected $privateKey = '';
	
	/**
	 * @var string $publicKey
	 *
	 * @access protected
	 */
	protected $publicKey = '';
	
	/**
	 *
	 */
	public function setKeys( $privateKey, $publicKey ){
		
		$this->privateKey = ( $privateKey );
		$this->publicKey  =( $publicKey );
		
		return $this;
	}
	
	/**
	 *
	 */
	public function setTheme( $theme="" ){
		$this->theme = $theme;
		return $this;
	}
	
	/**
	 *
	 */
    public function getReCapcha() {
        $reCaptcha = '';
        if( $this->publicKey && $this->privateKey ) {
            $reCaptcha = new Zend_Service_ReCaptcha( $this->publicKey, $this->privateKey );
            if( $this->theme ) {
                $reCaptcha->setOptions(array('theme' => $this->theme) );
            }
        }
        return $reCaptcha;
    }
	
	/**
	 *
	 */
    public function isValid( $challengeField, $responseField ) {
        $response = $this->getReCapcha()->verify( $challengeField, $responseField );
        return $response->isValid();
    }
}
?>