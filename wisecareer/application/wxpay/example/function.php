
<?php


function https_request($url,$data=null)
    {
      //1����ʼ��curl
      $ch = curl_init();
  
      //2�����ô���ѡ��
      curl_setopt($ch, CURLOPT_URL, $url);//�����url��ַ
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//������Ľ�����ļ�������ʽ����
        
      if(!empty($data))
      {
        curl_setopt($ch,CURLOPT_POST,1);//����POST��ʽ
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);//POST�ύ������
      }
  
      //3��ִ�����󲢴�����
      $outopt = curl_exec($ch);
  
      //��json����ת��������
      $outoptArr = json_decode($outopt,TRUE);
  
      //4���ر�curl
      curl_close($ch);
  
      //������صĽ��$outopt��json���ݣ�����Ҫ�ж�һ��
      if(is_array($outoptArr))
      {  
        return $outoptArr;
      }
      else
      {
        return $outopt;
      }      
    }