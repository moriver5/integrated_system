<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMail extends Mailable
{
	use Queueable, SerializesModels;

	protected $options;
	protected $data;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct($options, $data = [])
	{
		//
		$this->options	 = $options;
		$this->data		 = $data;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		//smtp指定ならそのsmtpを設定
		if( !empty($this->options['host_ip']) ){
			config(['mail.host' => $this->options['host_ip']]);
		}

		//port指定ならそのportを設定
		if( !empty($this->options['port']) ){
			config(['mail.port' => $this->options['port']]);
		}

		$this->withSwiftMessage(function ($message) {
			//送信元から設定
			list($account, $domain) = explode("@", $this->options['from']);

			//返信先設定
            $message->getHeaders()->addTextHeader('Reply-To', config('const.replay_to_mail_account').$domain);

			$account = config('const.return_path_to_mail_account');
			$return_email = $account.'@'.$domain;

			if( !empty($this->options['client_id']) ){
				$return_email = "{$account}.{$this->options['client_id']}@{$domain}";
			}

			//追加ヘッダ
			if( !empty($this->options['add_header']) ){
				if( is_array($this->options['add_header']) ){
					foreach($this->options['add_header'] as $header => $value){
						$message->getHeaders()->addTextHeader($header, $value);
					}
				}else{
					$message->getHeaders()->addTextHeader($header, $value);					
				}
			}

			//リターンパス設定
            $message->getHeaders()->addTextHeader('Return-Path', $return_email);
        });

		//HTMLメール
		if( isset($this->options['html_flg']) && $this->options['html_flg'] === true ){
			return $this->from($this->options['from'], $this->options['from_name'])
				->subject($this->options['subject'])
				->view($this->options['template'])
				->with($this->data);

		//プレーンメール
		}else{
			return $this->from($this->options['from'], $this->options['from_name'])
				->subject($this->options['subject'])
				->text($this->options['template'])
				->with($this->data);
		}
	}
}
