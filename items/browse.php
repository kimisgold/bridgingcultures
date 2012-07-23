<?php
$db = get_db();

if($itemTypeId = Zend_Controller_Front::getInstance()->getRequest()->getParam('type')) { 
    if(is_numeric($itemTypeId)) {
        $itemTypeName = $db->getTable('ItemType')->find($itemTypeId)->name;
    } else {
        $itemTypeName = $itemTypeId;
    }
    $title = 'Browse '. $itemTypeName . 's';
} else {
    $title = 'Browse Items';
} 

head(array('title'=>$title,'bodyid'=>'items','bodyclass' => 'browse')); ?>

<div class="browse-resource-header">

    <?php echo pagination_links(); ?>

    <h1><?php echo $title; ?> (<?php echo total_results(); ?> total)</h1>

    <?php if($itemTypeName == 'Book'): ?>

        <div id="books">

        <?php $i = 0; ?>
        <?php $books = get_items(array('type' => $itemTypeId, 'sort_field' => 'Dublin Core,Title'), 0); ?>
        <?php $otherTypes = get_items(array('tags' => 'bookshelf', 'sort_field' => 'Dublin Core,Title'),0) ?>
        <?php $all = array_merge($books,$otherTypes); ?>
        <?php set_items_for_loop($all); ?>
        
		<?php while (loop_items()): ?>
		
            <?php $i++; ?>
            
            <?php if(($i%3) === 1 || $i === 1) : ?>
                
            <div class="row">
                
            <?php endif; ?>
		
                <div class="book five columns">        
            
                    <div class="book-meta">
                
                    <?php if (item_fullsize()): ?>
                    	<div class="book-thumbnail two columns alpha">
                    	<?php echo link_to_item(item_fullsize()); ?>						
                    	</div>
                    <?php endif; ?>
                    
                    <p class="three columns omega"><?php echo link_to_item(
                        '<span class="book-title">' . item('Dublin Core', 'Title') . '</span><br>' . 
                        item('Dublin Core', 'Creator'), 				    
                        array('class'=>'permalink')); ?></p>
                    				
                    </div><!-- end class="book-meta" -->
                </div><!-- end class="book" -->
            
            <?php if(($i%3) === 0) : ?>
                
                </div>
                
            <?php endif; ?>
                        
		<?php endwhile; ?>
		
		</div>
		    
    <?php else: ?>
        
    <div id="page-menu" class="three columns alpha">
    
        <h2>Browse by</h2>
        
        <ul id="accordion" class="menu">
            <li><a href="#" class="top">Time Period</a>
                <?php 
                    $timePeriodId = $db->getTable('Element')->findByElementSetNameAndElementName('Item Type Metadata', 'Time Period')->id;   
                    $timePeriodTexts = explode("\n", $db->getTable('SimpleVocabTerm')->findByElementId($timePeriodId)->terms);
                    if($timePeriodTexts) {
                        echo '<ul class="sub-menu">';
                        foreach($timePeriodTexts as $timePeriodText) {
                            echo '<li>' . bc_link_to_items_with_element_text($timePeriodText, array(), 'browse', $timePeriodId, $timePeriodText) . '</li>';
                        }
                        echo '</ul>';
                    }   
                ?>                
            </li>
            <li><a href="#" class="top">Theme</a>
                <?php $collections = get_collections(); 
                set_collections_for_loop($collections);
                echo '<ul class="sub-menu">';
                while(loop_collections()):
                    $collection = get_current_collection();
                    echo '<li>' . link_to_items_in_collection($collection->name) . '</li>';
                endwhile;
                echo '</ul>';
                ?>
            </li>
            <li><a href="#" class="top">Item Type</a>
                <?php 
                    $allItemTypes = get_item_types();
                    $itemTypes = array();
                    foreach($allItemTypes as $itemType) {
                        $itemsTotal = $itemType->totalItems();
                        if($itemsTotal > 0) {
                            array_push($itemTypes,$itemType);
                        }
                    }
                    
                    if($itemTypes > 0): 
                    set_item_types_for_loop($itemTypes);
                    echo '<ul class="sub-menu">';
                        while(loop_item_types()):
                            $currentItemType = get_current_item_type();
                            echo '<li>'. link_to_items_with_item_type($currentItemType->name) . '</li>';
                        endwhile;
                    echo '</ul>';
                    endif; ?>
            </li>
            <li><a href="#" class="top">Region</a>
                <?php 
                    $regionId = $db->getTable('Element')->findByElementSetNameAndElementName('Item Type Metadata', 'Region')->id;   
                    $regionTexts = explode("\n", $db->getTable('SimpleVocabTerm')->findByElementId($regionId)->terms);
                    if($regionTexts) {
                        echo '<ul class="sub-menu">';
                        foreach($regionTexts as $regionText) {
                            echo '<li>' . bc_link_to_items_with_element_text($regionText, array(), 'browse', $regionId, $regionText) . '</li>';
                        }
                        echo '</ul>';
                    }   
                ?>                
                </li>
        </ul>
        
    </div>

    <div id="right-content" class="thirteen columns omega">
    
    <?php bc_display_filters(); ?>    
    
            
		<?php while (loop_items()): ?>
		
			<div class="item row">

				<div class="item-meta">
				
				    <div class="ten columns alpha">
				    
    			    	<h2><?php echo link_to_item(item('Dublin Core', 'Title'), array('class'=>'permalink')); ?></h2>
    			    	
    			    	<?php if ($text = item('Item Type Metadata', 'Text', array('snippet'=>250))): ?>
        		    		<div class="item-description">
        		    		<p><?php echo $text; ?></p>
        		    		</div>
    			    	<?php elseif ($description = item('Dublin Core', 'Description', array('snippet'=>250))): ?>
        		    		<div class="item-description">
        		    		<p><?php echo $description; ?></p>
        		    		</div>
    			    	<?php endif; ?>
                    
    			    	<?php if (item_has_tags()): ?>
        		    		<div class="tags"><p><strong>Tags:</strong>
        		    		<?php echo item_tags_as_string(); ?></p>
        		    		</div>
    			    	<?php endif; ?>
    			    	
				    </div>
                    
                    <div class="three columns omega">
				    
    				    <?php if (item_has_thumbnail()): ?>
        			    	<div class="item-thumbnail">
        			    	<?php echo link_to_item(item_square_thumbnail()); ?>						
        			    	</div>
    				    <?php endif; ?>
    				    				    
				    </div>				
				
    				<?php echo plugin_append_to_items_browse_each(); ?>

				</div><!-- end class="item-meta" -->

			</div><!-- end class="item row" -->
			
		<?php endwhile; ?>
		
    </div>
    
    <?php endif; ?>
    
</div>

</div>

<?php foot(); ?>