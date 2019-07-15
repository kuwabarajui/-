/**
 * �ėp�֐����C�u����
 *
 * @version 1.0.0
 * @author M.katsube <katsubemakito@gmail.com>
 * @copyright 2019 M.katsube
 */


/**
 * document.querySelector
 * (syntax sugar)
 *
 * @param  {string} id  �Z���N�^�[
 * @return {object}
 */
function $(id){
  return( document.querySelector(id) );
}

/**
 * RESTful API�����N�G�X�g
 *
 * @param  {string}   api      ���N�G�X�g����API��URL
 * @param  {function} callback �������Ɏ��s����callback
 * @return {void}
 */
function requestAPI(api, callback){
  const req = new XMLHttpRequest();
  req.open("GET", api, false);
  req.onreadystatechange = () => {
    if( req.status === 200 || req.status === 304 ){
    	const text = req.responseText;    // �T�[�o����n���ꂽ�l�����o��
      const json = JSON.parse(text);    // JS�ŗ��p�ł���悤�ϊ�

      // �G���[����
      if(json["status"] === false){
          alert("�G���[���������܂���");
          console.log(json);
          return(false);
      }

      // callback�����s
      callback(json["value"]);
    }
  }
  req.send();
}