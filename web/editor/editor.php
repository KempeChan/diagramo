<?
require_once dirname(__FILE__) . '/common/delegate.php';

if (!isset($_SESSION)) {
    session_start();
}

require_once dirname(__FILE__) . '/common/rememberme.php';


$delegate = new Delegate();

$loggedUser = $delegate->userGetById($_SESSION['userId']);

#print_r($_SESSION['userId']);

//start diagram guardian
if(isset($_REQUEST['diagramId']) && is_numeric($_REQUEST['diagramId'])){
    if(!is_object($loggedUser)){
        print "Not allocated to this diagram";
        exit();
    }        
}
//end diagram guardian

//get the address where the app reside
$WEBADDRESS = $delegate->settingsGetByKeyNative('WEBADDRESS');

$page = 'editor';
?>


<!DOCTYPE html>
<html>
    <!--Copyright 2010 Scriptoid s.r.l-->
    <head>
        <title>HTML5 diagram editor</title>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=9" />
        <script type="text/javascript" src="./assets/javascript/dropdownmenu.js"></script>    
        
        <link rel="stylesheet" media="screen" type="text/css" href="./assets/css/style.css" />
        <link rel="stylesheet" media="screen" type="text/css" href="./assets/css/minimap.css" />
        
        <script type="text/javascript" src="./assets/javascript/json2.js"></script>
        <script type="text/javascript" src="./assets/javascript/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="./assets/javascript/ajaxfileupload.js"></script>
        <script type="text/javascript" src="./assets/javascript/jquery.simplemodal-1.3.5.min.js"></script>
        
        
        <script type="text/javascript">
            /*Option 1:
             *We can use window.location like this:
             * url = window.location.protocol + window.location.hostname + ":" + window.location.port + ....
             * @see http://www.w3schools.com/jsref/obj_location.asp
             * 
             * Option 2:
             * Use http://code.google.com/p/js-uri/
             **/
            var figureSetsURL = '<?=$WEBADDRESS?>' + '/editor/lib/sets';
        </script>
        
        <script type="text/javascript" src="./lib/canvasprops.js"></script>
        <script type="text/javascript" src="./lib/style.js"></script>
        <script type="text/javascript" src="./lib/primitives.js"></script>
        <script type="text/javascript" src="./lib/ImageFrame.js"></script>
        <script type="text/javascript" src="./lib/matrix.js"></script>
        <script type="text/javascript" src="./lib/util.js"></script>
        <script type="text/javascript" src="./lib/key.js"></script>
        <script type="text/javascript" src="./lib/groups.js"></script>
        <script type="text/javascript" src="./lib/stack.js"></script>
        <script type="text/javascript" src="./lib/connections.js"></script>
        <script type="text/javascript" src="./lib/connectionManagers.js"></script>
        <script type="text/javascript" src="./lib/handles.js"></script>
        
        
        <script type="text/javascript" src="./lib/builder.js"></script>        
        <script type="text/javascript" src="./lib/text.js"></script>
        <script type="text/javascript" src="./lib/log.js"></script>
        <script type="text/javascript" src="./lib/text.js"></script>
        <script type="text/javascript" src="./lib/browserReady.js"></script>
        <script type="text/javascript" src="./lib/main.js"></script>
        
        <script type="text/javascript" src="./lib/sets/basic/basic.js"></script>
        <script type="text/javascript" src="./lib/sets/experimental/experimental.js"></script>
        <script type="text/javascript" src="./lib/sets/network/network.js"></script>
        <script type="text/javascript" src="./lib/sets/secondary/secondary.js"></script>
        <script type="text/javascript" src="./lib/sets/statemachine/statemachine.js"></script>
        
        <script type="text/javascript" src="./lib/minimap.js"></script>

        <script type="text/javascript" src="./lib/commands/History.js"></script>
        
        <script type="text/javascript" src="./lib/commands/FigureCreateCommand.js"></script>
        <script type="text/javascript" src="./lib/commands/FigureCloneCommand.js"></script>
        <script type="text/javascript" src="./lib/commands/FigureTranslateCommand.js"></script>
        <script type="text/javascript" src="./lib/commands/FigureRotateCommand.js"></script>
        <script type="text/javascript" src="./lib/commands/FigureScaleCommand.js"></script>
        <script type="text/javascript" src="./lib/commands/FigureZOrderCommand.js"></script>
        <script type="text/javascript" src="./lib/commands/FigureDeleteCommand.js"></script>
        
        <script type="text/javascript" src="./lib/commands/GroupRotateCommand.js"></script>
        <script type="text/javascript" src="./lib/commands/GroupScaleCommand.js"></script>
        <script type="text/javascript" src="./lib/commands/GroupCreateCommand.js"></script>
        <script type="text/javascript" src="./lib/commands/GroupCloneCommand.js"></script>
        <script type="text/javascript" src="./lib/commands/GroupDestroyCommand.js"></script>
        <script type="text/javascript" src="./lib/commands/GroupDeleteCommand.js"></script>
        <script type="text/javascript" src="./lib/commands/GroupTranslateCommand.js"></script>
        
        <script type="text/javascript" src="./lib/commands/ConnectorCreateCommand.js"></script>
        <script type="text/javascript" src="./lib/commands/ConnectorDeleteCommand.js"></script>                        
        
        <script type="text/javascript" src="./lib/commands/ConnectorAlterCommand.js"></script>
        
        <script type="text/javascript" src="./lib/commands/ShapeChangePropertyCommand.js"></script>
        
        <script type="text/javascript" src="./lib/commands/CanvasResizeCommand.js"></script>
        
        
        <script type="text/javascript" src="./assets/javascript/colorPicker_new.js"></script>
        <link rel="stylesheet" media="screen" type="text/css" href="./assets/css/colorPicker_new.css" />

        
        <!--[if IE]>
        <script src="./assets/javascript/excanvas.js"></script>
        <![endif]-->

        
    </head>
    <body onload="init('<?= isset($_REQUEST['diagramId']) && is_numeric($_REQUEST['diagramId']) ? $_REQUEST['diagramId']:''?>');" id="body">
        
        <? require_once dirname(__FILE__) . '/header.php'; ?>

        <div id="actions">
            
            

            <a style="text-decoration: none;" href="#" onclick="return save();" title="Save diagram (Ctrl-S)"><img src="assets/images/icon_save.jpg" border="0" width="16" height="16"/></a>
            
            <img class="separator" src="assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>
            
            <a style="text-decoration: none;" href="./myDiagrams.php" title="Open diagram"><img src="assets/images/icon_open.jpg" border="0" width="16" height="16"/></a>
            
            <img class="separator" src="assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>
            
            <a href="javascript:action('connector-straight');"  title="Straight connector"><img src="assets/images/icon_connector_straight.gif" border="0"/></a>

            <img class="separator" src="assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>
            
            <a href="javascript:action('connector-jagged');" title="Jagged connector"><img src="assets/images/icon_connector_jagged.gif" border="0"/></a>
            
            <img class="separator" src="assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>
            
            <a href="javascript:action('connector-organic');" title="Organic connector (Experimental)"><img src="assets/images/icon_connector_organic.gif" border="0" alt="Organic"/></a>
            
            <img class="separator" src="assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>            
                        
            <input type="checkbox" onclick="showGrid();" id="gridCheckbox"  title="Show grid" /> <span class="toolbarText">Show grid</span>
            
            <img class="separator" src="assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>
            
            <input type="checkbox" onclick="snapToGrid();" id="snapCheckbox" title="Snap elements to grid" /> <span class="toolbarText">Snap to grid</span>
            
            <img class="separator" src="assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>

            <a href="javascript:action('front');" title="Move to front"><img src="assets/images/icon_front.gif" border="0"/></a>
            
            <img class="separator" src="assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>
            
            <a href="javascript:action('back');" title="Move to back"><img src="assets/images/icon_back.gif" border="0"/></a>
            
            <img class="separator" src="assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>
            
            <a href="javascript:action('moveforward');" title="Move (one level) to front"><img src="assets/images/icon_forward.gif" border="0"/></a>
            
            <img class="separator" src="assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>
            
            <a href="javascript:action('moveback');" title="Move (one level) back"><img src="assets/images/icon_backward.gif" border="0"/></a>

            
            
            <img class="separator" src="assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>
            <a href="javascript:action('group');" title="Group (Ctrl-G)"><img src="assets/images/group.gif" border="0"/></a>
            
            <img class="separator" src="assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>
            <a href="javascript:action('ungroup');" title="Ungroup (Ctrl-U)"><img src="assets/images/ungroup.gif" border="0"/></a>

            <img class="separator" src="assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>
            
            <a href="javascript:createFigure(figure_Text);"  title="Add text"><img  src="assets/images/text.gif" border="0" height ="16"/></a>
            <img class="separator" src="assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>
            
<!--            <a href="javascript:createFigure(figure_SimpleImage);"  title="Add image"><img  style="vertical-align:middle;" src="/editor/assets/images/image.gif" border="0" height ="16" alt="Image"/></a>
            
            <img class="separator" src="assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>-->

            <a href="javascript:action('undo');" title="Undo (Ctrl-Z)"><img src="assets/images/arrow_undo.png" border="0"/></a>
            <!-- TODO: From Janis: we have to create a nice icon for duplicate, currently this is the only command without an icon -->
            <!--
            <a href="javascript:action('duplicate');">Copy (Ctrl-D)</a>
            -->
            
            <!-- <a href="javascript:action('redo');" title="Redo (Ctrl-Y)"><img src="assets/images/arrow_redo.png" border="0"/></a> -->
            <!--
            <input type="text" id="output" />                
            <img style="vertical-align:middle;" src="../assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>
            <a href="javascript:action('duplicate');">Copy</a>
            <img style="vertical-align:middle;" src="../assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>
            <a href="javascript:action('group');">Group</a>
            <img style="vertical-align:middle;" src="../assets/images/toolbar_separator.gif" border="0" width="1" height="16"/>
            <a href="javascript:action('ungroup');">Ungroup</a>
            -->
        </div>
        
        
        <div id="editor">
            <div id="figures">
                <select style="width: 120px;" onchange="setFigureSet(this.options[this.selectedIndex].value);">
                    <script>
                        for(var setName in figureSets){
                            var set = figureSets[setName];
                            document.write('<option value="' + setName + '">' + set['name'] + '</option>');
                        }
                    </script>

                </select>
                <script type="text/javascript">
                    var first = true;
                    for(var setName in figureSets){
                        document.write('<div id="' + setName + '" ' + (!first ? 'style="display: none"' : '')+'>');
                        document.write('<table border="0" cellpadding="0" cellspacing="0" width="120">');
                        var counter = 0;
                        var set = figureSets[setName];
                        for(var figure in set['figures']){
                            figure = set['figures'][figure];
                            if(counter % 3 === 0){
                                document.write('<tr>');
                            }
                            document.write('<td align="center"><a href="javascript:createFigure(figure_'+figure.figureFunction+');"><img src="lib/sets/'+setName+'/'+figure.image+'" border="0" alt="'+ figure.figureFunction + '" /></a></td>');
                            counter ++;
                            if(counter % 3 === 0){
                                document.write('</tr>');
                            }
                        }
                        if(counter % 3 !== 0){
                            document.write('</tr>');
                        }
                        document.write('</table></div>');
                        first = false;
                    }
                </script>                
            </div>
            
            <!--THE canvas-->
            <div style="width: 100%">
                <div  id="container">
                    <canvas id="a" width="800" height="600">
                        Your browser does not support HTML5. Please upgrade your browser to any modern version.
                    </canvas>
                </div>
            </div>
            
            <!--Right panel-->
            <div id="right">
                <center>
                    <div id="minimap">
                    </div>
                </center>
                <div style="overflow: scroll;" id="edit">
                </div>
            </div>
            
        </div>




        <script type="text/javascript">
            function loadFill(check){
                if(check.checked === true){
                    if($('#colorpickerHolder3').css('display') === 'none'){
                        $('#colorSelector3').click();
                    }
                }
                else{
                    if($('#colorpickerHolder3').css('display') === 'block'){
                        $('#colorSelector3').click();
                    }
                }
            }
        </script>
        <br/>
         <? //require_once dirname(__FILE__) . '/common/analytics.php';?>
    </body>
</html>
