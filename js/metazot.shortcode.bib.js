jQuery(document).ready(function()
{
	///////////////////////////////////////////////
	//
	//   METAZOT BIBLIOGRAPHY
	//
	///////////////////////////////////////////////

	if ( jQuery(".zp-Metazot-Bib").length > 0 )
	{
		jQuery(".zp-Metazot-Bib").each( function( index, instance )
		{
			var $instance = jQuery(instance);
      		var mz_params = {};

			mz_params.zpItemkey = false; if ( jQuery(".ZP_ITEM_KEY", $instance).text().trim().length > 0 ) mz_params.zpItemkey = jQuery(".ZP_ITEM_KEY", $instance).text();
			mz_params.zpCollectionId = false; if ( jQuery(".ZP_COLLECTION_ID", $instance).text().trim().length > 0 ) mz_params.zpCollectionId = jQuery(".ZP_COLLECTION_ID", $instance).text();
			mz_params.zpTagId = false; if ( jQuery(".ZP_TAG_ID", $instance).text().trim().length > 0 ) mz_params.zpTagId = jQuery(".ZP_TAG_ID", $instance).text();

			mz_params.zpAuthor = false; if ( jQuery(".ZP_AUTHOR", $instance).text().trim().length > 0 ) mz_params.zpAuthor = jQuery(".ZP_AUTHOR", $instance).text();
			mz_params.zpYear = false; if ( jQuery(".ZP_YEAR", $instance).text().trim().length > 0 ) mz_params.zpYear = jQuery(".ZP_YEAR", $instance).text();
			mz_params.zpStyle = false; if ( jQuery(".ZP_STYLE", $instance).text().trim().length > 0 ) mz_params.zpStyle = jQuery(".ZP_STYLE", $instance).text();
			mz_params.zpLimit = false; if ( jQuery(".ZP_LIMIT", $instance).text().trim().length > 0 ) mz_params.zpLimit = jQuery(".ZP_LIMIT", $instance).text();
			mz_params.zpTitle = false; if ( jQuery(".ZP_TITLE", $instance).text().trim().length > 0 ) mz_params. zpTitle = jQuery(".ZP_TITLE", $instance).text();

			mz_params.zpShowImages = false; if ( jQuery(".ZP_SHOWIMAGE", $instance).text().trim().length > 0 ) mz_params.zpShowImages = jQuery(".ZP_SHOWIMAGE", $instance).text().trim();
			mz_params.zpShowTags = false; if ( jQuery(".ZP_SHOWTAGS", $instance).text().trim().length > 0 ) mz_params.zpShowTags = true;
			mz_params.zpDownloadable = false; if ( jQuery(".ZP_DOWNLOADABLE", $instance).text().trim().length > 0 ) mz_params.zpDownloadable = true;
			mz_params.zpInclusive = false; if ( jQuery(".ZP_INCLUSIVE", $instance).text().trim().length > 0 ) mz_params.zpInclusive = true;
			mz_params.zpShowNotes = false; if ( jQuery(".ZP_NOTES", $instance).text().trim().length > 0 ) mz_params.zpShowNotes = true;
			mz_params.zpShowAbstracts = false; if ( jQuery(".ZP_ABSTRACT", $instance).text().trim().length > 0 ) mz_params.zpShowAbstracts = true;
			mz_params.zpCiteable = false; if ( jQuery(".ZP_CITEABLE", $instance).text().trim().length > 0 ) mz_params.zpCiteable = true;
			mz_params.zpTarget = false; if ( jQuery(".ZP_TARGET", $instance).text().trim().length > 0 ) mz_params.zpTarget = true;
			mz_params.zpURLWrap = false; if ( jQuery(".ZP_URLWRAP", $instance).text().trim().length > 0 ) mz_params.zpURLWrap = jQuery(".ZP_URLWRAP", $instance).text();
			mz_params.zpHighlight = false; if ( jQuery(".ZP_HIGHLIGHT", $instance).text().trim().length > 0 ) mz_params.zpHighlight = jQuery(".ZP_HIGHLIGHT", $instance).text();

			mz_params.zpForceNumsCount = 1;

			// Deal with multiples
			// Order of priority: collections, tags, authors, years
			// Filters (dealt with on shortcode.ajax.php): tags?, authors, years
			if ( mz_params.zpCollectionId && mz_params.zpCollectionId.indexOf(",") != -1 )
			{
				var tempCollections = mz_params.zpCollectionId.split(",");

				jQuery.each( tempCollections, function (i, collection)
				{
					mz_params.zpCollectionId = collection;
					mz_get_items ( 0, 0, $instance, mz_params, false ); // Get cached items first
				});
			}
			else
			{
				// Inclusive tags (treat exclusive normally)
				if ( mz_params.zpTagId && mz_params.zpInclusive == true && mz_params.zpTagId.indexOf(",") != -1 )
				{
					var tempTags = mz_params.zpTagId.split(",");

					jQuery.each( tempTags, function (i, tag)
					{
						mz_params.zpTagId = tag;
						mz_get_items ( 0, 0, $instance, mz_params, false ); // Get cached items first
					});
				}
				else
				{
					if ( mz_params.zpAuthor && mz_params.zpAuthor.indexOf(",") != -1 )
					{
						var tempAuthors = mz_params.zpAuthor.split(",");

						if ( mz_params.zpInclusive == true )
						{
							jQuery.each( tempAuthors, function (i, author)
							{
								mz_params.zpAuthor = author;
								mz_get_items ( 0, 0, $instance, mz_params, false );
							});
						}
						else // exclusive
						{
							mz_get_items ( 0, 0, $instance, mz_params, false );
						}
					}
					else
					{
						if ( mz_params.zpYear && mz_params.zpYear.indexOf(",") != -1 )
						{
							var tempYears = mz_params.zpYear.split(",");

							jQuery.each( tempYears, function (i, year)
							{
								mz_params.zpYear = year;
								mz_get_items ( 0, 0, $instance, mz_params, false );
							});
						}
						else // NORMAL, no multiples
						{
							mz_get_items ( 0, 0, $instance, mz_params, false );
						}
					}
				}
			}
		});
	} // Metazot Bibliography



	// Get list items:
	function mz_get_items ( request_start, request_last, $instance, params, update )
	{
		if ( typeof(request_start) === "undefined" || request_start == "false" || request_start == "" )
			request_start = 0;

		if ( typeof(request_last) === "undefined" || request_last == "false" || request_last == "" )
			request_last = 0;

		jQuery.ajax(
		{
			url: zpShortcodeAJAX.ajaxurl,
			ifModified: true,
			data: {
				'action': 'zpRetrieveViaShortcode',
				'instance_id': $instance.attr("id"),
				'api_user_id': jQuery(".ZP_API_USER_ID", $instance).text(),
				'item_type': jQuery(".ZP_DATATYPE", $instance).text(),

				'item_key': params.zpItemkey,
				'collection_id': params.zpCollectionId,
				'tag_id': params.zpTagId,

				'author': params.zpAuthor,
				'year': params.zpYear,
				'style': params.zpStyle,
				'limit': params.zpLimit,
				'title': params.zpTitle,

				'showimage': params.zpShowImages,
				'showtags': params.zpShowTags,
				'downloadable': params.zpDownloadable,
				'inclusive': params.zpInclusive,
				'shownotes': params.zpShowNotes,
				'showabstracts': params.zpShowAbstracts,
				'citeable': params.zpCiteable,

				'target': params.zpTarget,
				'urlwrap': params.zpURLWrap,
				'highlight': params.zpHighlight,

				'sort_by': jQuery(".ZP_SORTBY", $instance).text(),
				'order': jQuery(".ZP_ORDER", $instance).text(),

				'update': update,
				'request_start': request_start,
				'request_last': request_last,
				'zpShortcode_nonce': zpShortcodeAJAX.zpShortcode_nonce
			},
			xhrFields: {
				withCredentials: true
			},
			success: function(data)
			{
				var mz_items = jQuery.parseJSON( data );

				// First, display the items from this request, if any:
				if ( typeof mz_items != 'undefined' && mz_items != null && mz_items != 0 && mz_items.data.length > 0 )
				{
					var tempItems = "";
					if ( params.zpShowNotes == true ) var tempNotes = "";
					if ( params.zpTitle == true ) var tempTitle = "";


					// Indicate whether cache has been used:
					if ( update === false )
					{
						jQuery("#"+mz_items.instance+" .zp-List").addClass("used_cache");
					}
					else if ( update === true )
					{
						// Remove existing notes temporarily:
						if ( ! jQuery("#"+mz_items.instance+" .zp-List").hasClass("updating") && jQuery("#"+mz_items.instance+" .zp-Citation-Notes").length > 0 )
							jQuery("#"+mz_items.instance+" .zp-Citation-Notes").remove();

						if ( ! jQuery("#"+mz_items.instance+" .zp-List").hasClass("updating") )
							jQuery("#"+mz_items.instance+" .zp-List").addClass("updating");

						params.zpForceNumsCount = 1;
					}


					jQuery.each(mz_items.data, function( index, item )
					{
						var tempItem = "";

						// Determine item reference
						var $item_ref = jQuery("#"+mz_items.instance+" .zp-List #zp-ID-"+jQuery(".ZP_API_USER_ID", $instance).text()+"-"+item.key);

						// Replace or skip duplicates
						if ( $item_ref.length > 0 )
						{
							if ( update === false && ! jQuery("#"+mz_items.instance+" .zp-List").hasClass("used_cache") )
								return false;

							//if ( update === true && jQuery("#"+mz_items.instance+" .zp-List").hasClass("used_cache") )
							//	$item_ref.remove();
							//else
							//	return true;
						}

						// Year
						var tempItemYear = "0000";
						if ( item.meta.hasOwnProperty('parsedDate') )
							tempItemYear = item.meta.parsedDate.substring(0, 4);

						// Author
						var tempAuthor = item.data.title;
						if ( item.meta.hasOwnProperty('creatorSummary') )
							tempAuthor = item.meta.creatorSummary.replace( / /g, "-" );

						// Title
						if ( params.zpTitle == true )
						{
							// Update title and display
							if ( tempTitle != tempItemYear )
							{
								tempTitle = tempItemYear;
								tempItems += "<h3>"+tempTitle+"</h3>\n";
							}
						}

						tempItem += "<div id='zp-ID-"+jQuery(".ZP_API_USER_ID", $instance).text()+"-"+item.key+"'";
						tempItem += " data-zp-author-year='"+tempAuthor+"-"+tempItemYear+"'";
						tempItem += " data-zp-year-author='"+tempItemYear+"-"+tempAuthor+"'";
						tempItem += " class='zp-Entry zpSearchResultsItem";

						// Add update class to item
						if ( update === true ) tempItem += " mz_updated";

						// Image
						if ( jQuery("#"+mz_items.instance+" .ZP_SHOWIMAGE").text().trim().length > 0 && item.hasOwnProperty('image') )
						{
							tempItem += " zp-HasImage'>\n";
							tempItem += "<div id='zp-Citation-"+item.key+"' class='zp-Entry-Image hasImage' rel='"+item.key+"'>\n";

							// URL wrap image if applicable
							if ( params.zpURLWrap == "image" && item.data.url != "" )
							{
								tempItem += "<a href='"+item.data.url+"'";
								if ( params.zpTarget ) tempItem += " target='_blank'";
								tempItem += ">";
							}
							tempItem += "<img class='thumb' src='"+item.image[0]+"' alt='image' />\n";
							if ( params.zpURLWrap == "image" && item.data.url != "" ) tempItem += "</a>";
							tempItem += "</div><!-- .zp-Entry-Image -->\n";
						}
						else
						{
							tempItem += "'>\n";
						}

						// Force numbers
						if ( jQuery("#"+mz_items.instance+" .ZP_FORCENUM").text().length > 0 && jQuery("#"+mz_items.instance+" .ZP_FORCENUM").text() == "1" )
						{
							if ( ! /csl-left-margin/i.test(item.bib) ) // if existing style numbering not found
							{
								item.bib = item.bib.replace( '<div class="csl-entry">', '<div class="csl-entry">'+params.zpForceNumsCount+". " );
								params.zpForceNumsCount++;
							}
						}

						tempItem += item.bib;

						// Add abstracts, if any
						if ( params.zpShowAbstracts == true &&
								( item.data.hasOwnProperty('abstractNote') && item.data.abstractNote.length > 0 ) )
							tempItem +="<p class='zp-Abstract'><span class='zp-Abstract-Title'>Abstract:</span> " +item.data.abstractNote+ "</p>\n";

						// Add tags, if any
						if ( params.zpShowTags == true &&
								( item.data.hasOwnProperty('tags') && item.data.tags.length > 0 ) )
						{
							tempItem += "<p class='zp-Metazot-ShowTags'><span class='title'>Tags:</span> ";

							jQuery.each(item.data.tags, function ( tindex, tag )
							{
								tempItem += "<span class='tag'>" + tag.tag + "</span>";
								if ( tindex != (item.data.tags.length-1) ) tempItem += "<span class='separator'>,</span> ";
							});
							tempItem += "</p>\n";
						}

						tempItem += "</div><!-- .zp-Entry -->\n";

						// Add notes, if any
						if ( params.zpShowNotes == true && item.hasOwnProperty('notes') )
							tempNotes += item.notes;




						// Add this item to the list
						// Replace or skip duplicates
						if ( $item_ref.length > 0 && update === true )
						{
							$item_ref.replaceWith( jQuery( tempItem ) );
						}
						else
						{
							tempItems += tempItem;
						}

					}); // each item



					// Append cached/initial items to list:
					if ( update === false ) jQuery("#"+mz_items.instance+" .zp-List").append( tempItems );


					// Append notes to container:
					if ( params.zpShowNotes == true && tempNotes.length > 0 )
					{
						tempNotes = "<div class='zp-Citation-Notes'>\n<hr><h3>Notes</h3>\n<ol>\n" + tempNotes;
						tempNotes = tempNotes + "</ol>\n</div><!-- .zp-Citation-Notes -->\n\n";

						jQuery("#"+mz_items.instance).append( tempNotes );
					}


					// Fix incorrect numbering in existing numbered style
					if ( jQuery("#"+mz_items.instance+" .zp-List .csl-left-margin").length > 0 )
					{
						params.zpForceNumsCount = 1; // UNSURE: 0?

						jQuery("#"+mz_items.instance+" .zp-List .csl-left-margin").each(function ( index, item )
						{
							var item_content = jQuery(item).text();
							item_content = item_content.replace( item_content.match(/\d+/)[0], params.zpForceNumsCount );
							jQuery(item).text( item_content );

							params.zpForceNumsCount++;
						});
					}

					// Then, continue with other requests, if they exist
					if ( mz_items.meta.request_next != false && mz_items.meta.request_next != "false" )
					{
						mz_get_items ( mz_items.meta.request_next, mz_items.meta.request_last, $instance, params, update );
					}
					else
					{
						// Remove loading
						jQuery("#"+mz_items.instance+" .zp-List").removeClass("loading");

						// Check for updates
						if ( ! jQuery("#"+mz_items.instance+" .zp-List").hasClass("updating") )
						{
							mz_get_items ( 0, 0, $instance, params, true );
						}

						// Otherwise finish up and re-sort if needed
						// Sort based on Trent's: http://trentrichardson.com/2013/12/16/sort-dom-elements-jquery/
						else
						{
							var sortby = jQuery(".ZP_SORTBY", $instance).text();
							var orderby = jQuery(".ZP_ORDER", $instance).text();

							// Re-sort if not numbered and sorting by author or date
							if ( ["author","date"].indexOf(sortby) !== -1 && jQuery("#"+mz_items.instance+" .zp-List .csl-left-margin").length == 0 )
							{
								var sortOrder = "data-zp-author-year";
								if ( sortby == "date") sortOrder = "data-zp-year-author";

								jQuery("#"+mz_items.instance+" .zp-List div.zp-Entry").sort(function(a,b)
								{
									var an = a.getAttribute(sortOrder).toLowerCase(),
										bn = b.getAttribute(sortOrder).toLowerCase();

									if (an > bn)
										if ( orderby == "asc" )
											return 1;
										else
											return -1;
									else if (an < bn)
										if ( orderby == "asc" )
											return -1;
										else
											return 1;
									else
										return 0;

								}).detach().appendTo("#"+mz_items.instance+" .zp-List");
							}
						}
					}
				}

				// Message that there's no items
				else
				{
					if ( update === true )
					{
						jQuery("#"+$instance.attr("id")+" .zp-List").removeClass("loading");

                        if ( jQuery("#"+$instance.attr("id")+" .zp-List .zp-Entry").length == 0 )
							jQuery("#"+$instance.attr("id")+" .zp-List").append("<p>There are no citations to display.</p>\n");
					}
				}
			},
			error: function(errorThrown)
			{
                console.log("Error: ");
				console.log(errorThrown);
			}
		});

	} // function mz_get_items

});
