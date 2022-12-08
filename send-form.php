<?php

/**
 * Clean incoming value from trash.
 *
 * @param	mixed	$value	Some value to clean.
 * @return	mixed	$value	The same value, but cleaned.
 */
function as_clean_value( $value )
{
	$value = trim( $value );
	$value = stripslashes( $value );
	$value = strip_tags( $value );

	return htmlspecialchars( $value );
}

/**
 * Function checks if value length is between min and max parameters.
 *
 * @param   string	$value  Specific string..
 * @param   int		$min    Minimum symbols value length.
 * @param   int		$max	Maximum symbols value length.
 * @return  bool            True if OK, false if value length is too small or large.
 */
function as_check_length( string $value, int $min, int $max ): bool
{
	return ! ( mb_strlen( $value ) < $min || mb_strlen( $value ) > $max );
}



/**
 * Function checks phone symbols.
 *
 * @param   string  $phone  Some phone number.
 * @return  bool            True if OK, false if string has bad symbols.
 */
function as_check_phone( string $phone ): bool
{
	return preg_match('/^[0-9()+\-\s]+$/iu', $phone );
}

$person_phone	= isset( $_POST['person-phone'] ) ? as_clean_value( $_POST['person-phone'] ) : null;

if( ! as_check_length( $person_phone, 3, 30 ) ){
	echo json_encode( [
		'success'	=> 0,
		'message'	=> 'Телефон не должен превышать 30 символов или быть меньше 3 символов.'
	] );
	die();
}

// Check phone symbols.
if( ! as_check_phone( $person_phone ) ){
	echo json_encode( [
		'success'	=> 0,
		'message'	=> 'Пожалуйста, введите корректный телефон.'
	] );
	die();
}

// Prepare message for mail.
$message = "Привет!\n" .
	"Форма обратной связи:\n\n" .
	"Телефон - $person_phone\n" .
// Mail headers.
$headers = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n" .
	"Reply-To: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n" .
	"X-Mailer: PHP/" . phpversion();

$result = mail('some@mail.com', 'Форма обратной связи', $message, $headers );

if( $result )
	echo json_encode( [
		'success'	=> 1,
		'message'	=> 'Спасибо за Ваше сообщение! Мы свяжемся с Вами в ближайшее время.'
	] );	// Success.
else
	echo json_encode( [
		'success'	=> 0,
		'message'	=> 'Ошибка отправки. Пожалуйста, попробуйте позже.'
	] );	// Failed.

die();

