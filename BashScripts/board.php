<?php 
//error_reporting(0);
$con = mysql_connect("localhost", "root", "pradeep");
mysql_select_db("vlcc_q", $con);
/*$con = mysql_connect("localhost", "root", "");
mysql_select_db("vlcc_q", $con);*/
require 'class.phpmailer.php';
require 'class.smtp.php';
require 'html2pdf.class.php';
$id=$_REQUEST['id'];
?>
<!DOCTYPE html>
<html>
<head>
<title>VLCC HEALTH CARE LIMITED</title>
<style type="text/css">
      .wrapper
     {
         width: 900px;
         font-family: calibri;
         margin: auto;
         background: #fffff7;
         border: 1px solid #ddd;
         padding: 15px;
     }
     .input-box{
         padding: 4px;
         width: 97%;
     }
     .input-box1{
         padding: 4px;
         width: 93%;
     }
     table{
         border-spacing: 0;
         border-collapse: collapse;
     }
    .btn-theme{
        border: 0px;
        background: #f36f21;
        padding: 5px 10px;
        color: #fff;
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
    }
    .btn-theme:hover{
        cursor: pointer;
        opacity: 0.9;
    }
</style>
</head> 
<body>
<form name="form1" method="POST" action="" >
<div class="wrapper" >
         <table width="100%">
           <tr>
             <th>&nbsp;</th>
           </tr>
           <tr>
             <th style="text-transform: uppercase;">PERFORMANCE EVALUATION FORM FOR BOARD</th>
           </tr>
           <tr>
            <th><i>Scores go from 1 = Low/ Strongly Disagree to 5 = High/Strongly Agree</i></th>
           </tr>
           <tr>
            <th>Evaluation Period 1st April 2017 to 31st March 2018</th>
           </tr>
         </table>
         <table width="100%" border="1" cellspacing="0">
          <tr style="background-color:#f36f21;">
             <td width="5%" style=" height: 42px; border-left: 0px;" align="center"><strong>S. No.</strong></td>
             <td width="60%" style="padding: 12px;"><strong>Evaluation Crieteria</strong></td>
             <td colspan="5" align="center"><strong>Rating</strong></td>
           </tr>
           <tr>
             
             <td width="60%" style="padding: 12px; height: 10px; border-left: 0px;" colspan="2"></td>
             <td width="7%" align="center">1</td>
             <td width="7%" align="center">2</td>
             <td width="7%" align="center">3</td>
             <td width="7%" align="center">4</td>
             <td width="7%" align="center" style=" border-right:0px;">5</td>
           </tr>
           <tr>
             <td width="5%" style=" height: 42px; border-left: 0px;" align="center">1</td>
             <td width="60%" style="padding: 12px;">The Board has appropriate expertise and experience to meet the best interests of the company?</td>
             <td width="7%" align="center"><input type="radio" name="rd1"  value="1" onClick="boacal(this.value,this.name)"></td>
             <td width="7%" align="center"><input type="radio" name="rd1" value="2" onClick="boacal(this.value,this.name)"></td>
             <td width="7%" align="center"><input type="radio" name="rd1" value="3" onClick="boacal(this.value,this.name)"></td>
             <td width="7%" align="center"><input type="radio" name="rd1" value="4" onClick="boacal(this.value,this.name)"></td>
             <td width="7%" align="center" style=" border-right:0px;"><input type="radio" name="rd1" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">2</td>
             <td style="padding: 12px;">The board has appropriate combination of industry knowledge and diversity (gender, experience, background)?</td>
             <td align="center"><input type="radio" name="rd2" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd2" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd2" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd2" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd2" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">3</td>
             <td style="padding: 12px;">The Board size adequate as regards the size of the company and area of its operation?</td>
             <td align="center"><input type="radio" name="rd3" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd3" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd3" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd3" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd3" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">4</td>
             <td style="padding: 12px;">The Board is balanced as regards composition, i.e. Board has appropriate mixture of executive, non-executive directors and Independent Directors.</td>
             <td align="center"><input type="radio" name="rd4" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd4" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd4" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd4" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd4" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">5</td>
             <td style="padding: 12px;">The Board spend sufficient time in understanding strategic and business plans, and provides critical oversight on the same.</td>
             <td align="center"><input type="radio" name="rd5" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd5" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd5" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd5" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd5" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">6</td>
             <td style="padding: 12px;">The Board has the proper number of committees as required by legislation and guidelines, with well-defined terms of reference and reporting requirements.</td>
             <td align="center"><input type="radio" name="rd6" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd6" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd6" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd6" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd6" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           
            <tr>
             <td align="center" style="border-left: 0px;">7</td>
             <td style="padding: 12px;">Do the committees of the Board that meet regularly and report to the Board?</td>
             <td align="center"><input type="radio" name="rd7" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd7" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd7" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd7" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd7" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
         </table>
         <h3 style="margin-left: 10px;">Board Meetings and Procedures</h3>
         <table width="100%" border="1" cellspacing="0">
           <tr>
             <td width="5%" style=" height: 42px; border-left: 0px;" align="center">8</td>
             <td width="60%" style="padding: 12px;">The Board members receive meeting agendas and supporting materials in time for adequate review.</td>
             <td width="7%" align="center"><input type="radio" name="rd8" value="1" onClick="boacal(this.value,this.name)"></td>
             <td width="7%" align="center"><input type="radio" name="rd8" value="2" onClick="boacal(this.value,this.name)"></td>
             <td width="7%" align="center"><input type="radio" name="rd8" value="3" onClick="boacal(this.value,this.name)"></td>
             <td width="7%" align="center"><input type="radio" name="rd8" value="4" onClick="boacal(this.value,this.name)"></td>
             <td width="7%" align="center" style=" border-right:0px;"><input type="radio" name="rd8" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">9</td>
             <td style="padding: 12px;">The Board meeting agenda and related background papers are concise and provide information of appropriate quality and detail.</td>
            <td align="center"><input type="radio" name="rd9" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd9" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd9" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd9" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd9" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">10</td>
             <td style="padding: 12px;">Frequency of Board Meetings is adequate.</td>
             <td align="center"><input type="radio" name="rd10" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd10" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd10" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd10" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd10" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">11</td>
             <td style="padding: 12px;">The facility for video conferencing for conducting meetings is provided, whenever required.</td>
            <td align="center"><input type="radio" name="rd11" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd11" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd11" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd11" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd11" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">12</td>
             <td style="padding: 12px;">The Board meetings encourage a high quality of discussions and decision making.</td>
             <td align="center"><input type="radio" name="rd12" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd12" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd12" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd12" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd12" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td width="5%" style=" height: 42px; border-left: 0px;" align="center">13</td>
             <td width="51%" style="padding: 12px;">Do the meetings encourage open participation and discussion so that each member of the Board gets adequate opportunity to put up his views before the Board and have his views taken into account before a conclusion is arrived at?</td>
            <td align="center"><input type="radio" name="rd13" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd13" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd13" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd13" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd13" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">14</td>
             <td style="padding: 12px;">Does the Management Team remain present at the meeting to present relevant information to the Board as and when any agenda matter required.</td>
            <td align="center"><input type="radio" name="rd14" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd14" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd14" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd14" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd14" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">15</td>
             <td style="padding: 12px;">The amount of time spent on discussions on strategic and general issues is sufficient.</td>
            <td align="center"><input type="radio" name="rd15" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd15" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd15" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd15" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd15" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">16</td>
             <td style="padding: 12px;">How effectively does the Board works collectively as a team in the best interest of the company?</td>
            <td align="center"><input type="radio" name="rd16" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd16" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd16" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd16" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd16" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">17</td>
             <td style="padding: 12px;">The minutes of Board meetings are clear, accurate and timely.</td>
            <td align="center"><input type="radio" name="rd17" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd17" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd17" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd17" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd17" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">18</td>
             <td style="padding: 12px;">The actions arising from board meetings are properly followed up and reviewed in subsequent board meetings.</td>
            <td align="center"><input type="radio" name="rd18" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd18" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd18" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd18" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd18" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
         </table>
         <h3 style="margin-left: 10px;">Board Strategy and Risk Management</h3>
         <table width="100%" border="1" cellspacing="0">
           <tr>
             <td width="5%" style=" height: 42px; border-left: 0px;" align="center">19</td>
             <td width="60%" style="padding: 12px;">The Board spend time on issues relating to the strategic direction and not day-today management responsibilities</td>
             <td width="7%" align="center"><input type="radio" name="rd19" value="1" onClick="boacal(this.value,this.name)"></td>
             <td width="7%" align="center"><input type="radio" name="rd19" value="2" onClick="boacal(this.value,this.name)"></td>
             <td width="7%" align="center"><input type="radio" name="rd19" value="3" onClick="boacal(this.value,this.name)"></td>
             <td width="7%" align="center"><input type="radio" name="rd19" value="4" onClick="boacal(this.value,this.name)"></td>
             <td width="7%" align="center" style=" border-right:0px;"><input type="radio" name="rd19" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">20</td>
             <td style="padding: 12px;">The Board engage with management in the strategic planning process, including corporate goals, objectives and overall operating and financial plans to achieve them.</td>
            <td align="center"><input type="radio" name="rd20" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd20" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd20" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd20" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd20" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">21</td>
             <td style="padding: 12px;">The Board monitors the implementation of the long term strategic goals.</td>
             <td align="center"><input type="radio" name="rd21" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd21" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd21" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd21" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd21" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">22</td>
             <td style="padding: 12px;">The Board monitors the company&#39;s internal controls and compliance with applicable laws and regulations.</td>
            <td align="center"><input type="radio" name="rd22" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd22" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd22" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd22" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd22" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">23</td>
             <td style="padding: 12px;">The Board focuses its attention on long-term policy issues rather than short-term administrative matters.</td>
             <td align="center"><input type="radio" name="rd23" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd23" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd23" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd23" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd23" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td width="5%" style=" height: 42px; border-left: 0px;" align="center">24</td>
             <td width="51%" style="padding: 12px;">The Board discusses thoroughly the annual budget of the Company and its implications before approving it.</td>
            <td align="center"><input type="radio" name="rd24" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd24" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd24" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd24" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd24" value="5" onClick="boacal(this.value,this.name)"></td>
        
           </tr>
           <tr>
             <td align="center" style="border-left: 0px;">25</td>
             <td style="padding: 12px;">The Board periodically reviews the actual result of the Company vis-à-vis the plan/ policies devised earlier and suggests corrective measures, if required.
</td>
            <td align="center"><input type="radio" name="rd25" value="1" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd25" value="2" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd25" value="3" onClick="boacal(this.value,this.name)"></td>
             <td align="center"><input type="radio" name="rd25" value="4" onClick="boacal(this.value,this.name)"></td>
             <td align="center" style=" border-right:0px;"><input type="radio" name="rd25" value="5" onClick="boacal(this.value,this.name)"></td>
           </tr>
           <tr>
             <td colspan="4" style="text-align: right; padding: 10px; color: #fff; padding-right: 5%; background: #0b4bc1;"><strong>Overall rating of Board performance</strong></td>
             <input type="hidden" id="div5" name="div5" value="">
             <td colspan="3" align="center" style="padding: 10px; color: #fff; background: #0b4bc1;"><div id="div2"></div></td>
           </tr>
            <tr>
             <td colspan="7" align="right" style="padding:10px;"><input type="submit" name="btn_save" id="btn_save" class="btn btn-theme" value="Submit" /></td>
           </tr>
         </table>
</div>
	</form>
</body>
</html>
<script language="javascript">
function boacal(id,va)
{
	var tot = 0;
	var SelCount = 0;
	for(var k=1; k<=25; k++)
	{
		if(document.querySelector('input[name=rd'+k+']:checked'))
		{
			var sel = document.querySelector('input[name=rd'+k+']:checked').value;
			SelCount = parseInt(SelCount) + parseInt(sel);
			tot = parseInt(tot) + 1;
		}
	}
	var AverageFirst = SelCount/tot;
	var divobj = document.getElementById('div2');
    divobj.style.display='block';
    divobj.innerHTML = AverageFirst.toFixed(2);
	document.getElementById('div5').value=AverageFirst.toFixed(2);
} 

</script>
<?php
if($_SERVER['REQUEST_METHOD']=="POST" && $_REQUEST['btn_save']=='Submit')
{
		$result=mysql_query("insert into board_question (main_id,board_avrage,ra1, ra2, ra3, ra4, ra5, ra6, ra7, ra8, ra9, ra10, ra11, ra12, ra13, ra14, ra15, ra16, ra17, ra18, ra19, ra20, ra21, ra22, ra23, ra24, ra25, date) values ('".$id."','".$_REQUEST['div5']."','".$_REQUEST['rd1']."','".$_REQUEST['rd2']."','".$_REQUEST['rd3']."','".$_REQUEST['rd4']."','".$_REQUEST['rd5']."','".$_REQUEST['rd6']."','".$_REQUEST['rd7']."','".$_REQUEST['rd8']."','".$_REQUEST['rd9']."','".$_REQUEST['rd10']."','".$_REQUEST['rd11']."','".$_REQUEST['rd12']."','".$_REQUEST['rd13']."','".$_REQUEST['rd14']."','".$_REQUEST['rd15']."','".$_REQUEST['rd16']."','".$_REQUEST['rd17']."','".$_REQUEST['rd18']."','".$_REQUEST['rd19']."','".$_REQUEST['rd20']."','".$_REQUEST['rd21']."','".$_REQUEST['rd22']."','".$_REQUEST['rd23']."','".$_REQUEST['rd24']."','".$_REQUEST['rd25']."','".date('Y-m-d')."')");
		
		if($result==1)
		{
				$message="<page>
				<div style='width: 750px; margin: auto; background: #fffff7;  border: 1px solid #ddd; margin-right:20px;'>
				 <table width='100%' align='center'>
				    
				   <tr>
					 <th align='center' style='text-transform: uppercase;' align='center'>VLCC HEALTH CARE LIMITED SHAILESH</th>
				   </tr>
				   <tr>
					<th align='center' style='text-transform: uppercase;' align='center'>independent DIRECTORS PERFORMANCE EVALUATION FORM</th>
				   </tr>
				   <tr>
					<th>&nbsp;</th>
				   </tr>
				 </table>
		        </div>
		        </page>";
				//print $message;
				//die;
				    set_time_limit(0);
					//$to='raviraj.b@vlccwellness.com';
				    // $cc='shubhra.singh@vlccwellness.com';
					//$cc1='pradeep@vallesoft.com';
					//$cc2='shubham@vallesoft.com';
					$to='shaileshsingh5555@gmail.com';
					$cc='shailesh@vallesoft.com';
					//currently woking code
					$content = ob_get_clean();
					try
					{
						$html2pdf = new HTML2PDF('P', 'A4', 'fr');
						$html2pdf->setDefaultFont('Arial');
						$html2pdf->writeHTML($message);
					    //$html2pdf->Output('salary_slip_details.pdf');
					}
					catch(HTML2PDF_exception $e) {
					echo $e;
					exit;
					}
					$mail = new PHPMailer(); // create a new object
					$mail->IsSMTP(); // enable SMTP
					$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
					$mail->SMTPAuth = true; // authentication enabled
					$mail->SMTPSecure = 'TLS'; // secure transfer enabled REQUIRED for Gmail or ssl or tsl
					$mail->Host = "https://mail.vlccwellness.com";
					$mail->Port = 587; // or 587,465
					$mail->IsHTML(true);
				    $mail->Username = 'info1@vlccwellness.com';	
					$mail->Password = 'INFO#$^$ss*128';
					$mail->From="info1@vlccwellness.com";
					//$mail->FromName="Bookworm Central";
					$mail->Sender="info1@vlccwellness.com";
					$mail->Subject = "Performance evaluation";
					$mail->Body = $message;
					$mail->AddAddress($to);  
					$mail->AddAddress($cc); 
					//$mail->AddAddress($cc1); 
					//$mail->AddAddress($cc2); 
					/*$mail->Timeout = 3600; 
					$mail->separator = md5(time());
					$mail->eol = PHP_EOL;
					$mail->filename = 'dfsdf.pdf';
					if (ob_get_contents()) ob_end_clean();
					$mail->pdfdoc = $html2pdf->Output("Performance evaluation form.pdf", "F");
					
					$mail->AddAttachment('Performance evaluation form.pdf');
					*/
					$mail->IsHTML(true);      
					
					 if(!$mail->Send()) {
					 echo	$error = 'Mail error: '.$mail->ErrorInfo;
						return false;
					 } else {
						echo "Message has been sent.";
					 }
			
			echo 'fdddddddddddddd';
			die;
				print("<script language='javascript'>alert('Thank You! ');window.location.href='board.php';</script>");	
			}
	
}

?>