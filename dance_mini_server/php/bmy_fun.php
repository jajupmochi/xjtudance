<?php  
/*******************************************************************************
兵马俑BBS相关函数
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-08-15
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

/**
* 返回微信小程序报到内容。
* @param string $id id
* @param string $nickname 昵称
* @param string $gender 性别
* @param string $height 身高
* @param string $grade 年级
* @param string $major 专业
* @param string $hometown 家乡
* @param string $selfIntro 自我介绍
* @param string $danceLevel 舞蹈水平
* @return string 报到内容
* @access public
*/
function wxminiBaodao($id, $nickname, $gender, $height, $grade, $major, $hometown, $selfIntro, $danceLevel) {
	$id = ($id == 'jiaodadance' ? '小dance代发' : $id);
	return "[0;1;31m[I您的id是:[m\n".$id.
		"\n\n[0;1;32m[I昵称呢?:[m\n".$nickname.
		"\n\n[0;1;33m[I性别:[m\n".$gender.
		"\n\n[0;1;34m[I身高可别忘了:[m\n".$height.
		"\n\n[0;1;35m[I学院/专业:[m\n".$major.
		"\n\n[0;1;36m[I年级:[m\n".$grade.
		"\n\n[0;1;31m[I家乡:[m\n".$hometown.
		"\n\n[0;1;32m[I再介绍一下自己啦:[m\n".$selfIntro.
		"\n\n[0;1;33m[I您的舞蹈水平(参加培训情况等)/擅长或喜欢的舞种?:[m\n".$danceLevel.
		"\n\n[0;1;34m[I打开微信小程序\"西交dance\"查看美照啦~[m";
}

/**
* 微信小程序水印，从小程序发文到兵马俑BBS时添加到文末。
* @param string $time 时间
* @param MongoDB $db mongo数据库
* @param string $level 用户等级
* @return string 水印
* @access public
*/
function wxminiWatermark4bmy($time, $db, $level) {
	$time = explode('.', $time);
	$time = $time[0];
	$time = substr_replace($time, '-', 4, 0);
	$time = substr_replace($time, '-', 7, 0);
	$time = substr_replace($time, ' ', 10, 0);
	$time = substr_replace($time, ':', 13, 0);
	$time = substr_replace($time, ':', 16, 0);
	$watermark = "\n\n
			[1;34m********************************************************************************[m
			[1;33m".$time."[m
			[0;36m我从[m [5m[0;35mdance微信小程序 - 西交dance[m[m [0;36m发来这篇文章[m
			[0;36m这是dance的第[m[1;36m[4m".($db->diaries->count() + 1)."[m[m[0;36m篇文章[m\n
			[0;36m我与[m[1;32m[4m".$db->users->count()."[m[m[0;36m位舞友在dance切磋[m
			[0;36m我的称号是[m[0;31m[4m".$level."[m[m
			[1;34m********************************************************************************[m";
	return $watermark;
}

/*
╔══╮╭══╮╭╮╭╮╭══╮╭══╮
║╭╮║║╭╮║║╰╮║║╭═╯║╭═╯
║║║║║╰╯║║　　║║║　　║╰═╮
║║║║║╭╮║║　　║║║　　║╭═╯
║╰╯║║║║║║╰╮║║╰═╮║╰═╮
╚══╯╰╯╰╯╰╯╰╯╰══╯╰══╯
╗╗╦╔╗╗╭╔═╯═╗　　╔╗　　╔╦╔══╗╔══╩═╗
╯╚╩╯╠╝║╔═══╗╔╗　║╔╗╔╠║　　║║╔═══╗
╯╔╩╗║║║╔═══╗║　　║　║╭╣╚══╯║　　╮═╯
║║╭╯╭╝╯╔═══╗║　　║　║║╠╔═╦╗║╔═╩╦╗
║║║║║║║║　　　║║　╔║　║║║　═╠　║　　　║　
╰╝╚╯╯╝╚╚═══╯╚╯╰╝╰╝╚╚╚═╩╝╯　╚═╯　

############################################################################################################
#                                                                                                          #
#        ii                                                                                                #
#      LDDLD                                                                                               #
#     EEWWKWE#                                                                                             #
#     EKWfff#W                                                                                             #
#    ,KWGfE#W:                                                                                             #
#    WWWffff##                                                                                             #
#    ;#WKffL#W         fLj                                                                                 #
#     W#ffjDWL      fEKDLfG,                                                                               #
#      Gtff#GDDE ti######, :                                                                               #
#      DtfLE,LDEL#########  D                                                                              #
#     DGjffD,,EW######K###   G                                                                             #
#    fLDDDD,,,;#######LfLE,  Gf                                                                            #
#    fLG,,,,,,;######GLiEW    G            ##, ##                                                          #
#   jLLG:,,,,,;jE#####GfWf  LLf            ##   ##f                                                        #
#   ,LLG:,,,,,,i;########GDLG.             ##   W##  D##### ### ###   :###i  t###G                         #
#    LGG,,,,,,,;#EG# jDDEGKD              t##   W## ##  f#E  ##, ##  W#  ## ##  ##                         #
#   DLGEi:,,,,,; :W  fLGEGG               ##i   ### ##  ##   ##  ##  ##  #E ##  #f                         #
#  ,LGD ,:,,,,,;    LLDGEEEE              ##    ## f##  ##   ##  ##  ##    f###i                           #
#  GGGD  :,,,,,i   Li  DEEEEt             ##   ##G D##  ##  W#i :##  ##   ,t##   ,                         #
#  LffD ,,:j,,,i,GD    GEEEE             ,##  ##    ## ###  ##  ,##  ### #  ##i #                          #
#   ffL ,::LGfGf       EEEE                          #  :#       W#    #i    f#                            #
#   :LL  ,:,j:,i       KEE:                                                                                #
#    iLG ,::,,,,      .EE:                                                                                 #
#     iL::,G,,,;  jEDDDE:                                                                                  #
#      :GGLiGD;; GGDDEEE                                                                                   #
#       i;GL:,,E DDDEEEE                                                                                   #
#      EEL:,:fED DDEEEEE                                                                                   #
#      DDEEEEDDD EDEEEEE                                                                                   #
#      DDDDDDDDK EDEEEEEt                  #  #  ##     ##   #          ##      #####D#####,       #i      #
#     DGDDDDDDD  KDEEEEEE                ,# #D## #      #########f      #,      # #  #f  ,#   ##########.  #
#     GGDDKKEDD  EDEEEEEE               t# :## #####   #f#      #       #        ,#  #   ##  t#            #
#    jGGDDGGGDE  KDEEEEEEK                ####### #   ## #######    ## ## #    #####D#####   #########G    #
#    DLDDDLGDG   EDEEEEEEEt              # #####.#E  ###            #  #. #E     #           #   ####      #
#    GLDEGLGDEG fEEEEEEEEEEt            ##    ## #   ##i ######,   #E  #  ##    ###f######  D#   W#f       #
#   .LGDELLGE LGEDEEEEEEEEEEKKKE       W#Et### ###    #           ##  ##  ##   ##D#  :#     #G########f    #
#   jLGEGLGDE  LEEEEEEEEEEEEKKKj        # #,#  ##    D# #######  ##   #   W#  ### #######  ,#    #f ##     #
#   LfGDEDDDE   EEEEEEEEEEEKKE:         # # ## #     #, #    #   t   :#       #f#  # # :E  #G    #         #
#   DDEDEDGDEK  ;EEEEEEEEKKKKf         #G#,######    # W######       ##        #f   ,#    ##    W#         #
#     WEEDDEEEf  EEEEEEEKEK            # # ### t#,  ## #i   ##     f##         # ######## #    ##.         #
#       KDDDEED   EEEEEED:                                                    f#                           #
#       KDDEEEf    DE,                                                                                     #
#       GDE EDi    LL.                                                                                     #
#      GDDi K,.    GL.                                                                                     #
#     LGDD  ;;     fL:                                                                                     #
#     tDDE  ,       fi                                                                                     #
#     EDDK          jf                                                                                     #
#    jDDDE           G                                                                                     #
#   .GDDD            i                                                                                     #
#   DGEDf            :                                                                                     #
#  ,,i,iD             i                                                                                    #
#                     D                                                                                    #
#                     D.                                                                                   #
#                     ;j                                                                                   #
#                                                                                                          #
############################################################################################################


*/
?>  
