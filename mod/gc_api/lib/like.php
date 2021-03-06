<?php

elgg_ws_expose_function(
	"like.item",
	"like_item",
	array(
		"user" => array('type' => 'string', 'required' => true),
		"guid" => array('type' => 'int', 'required' => true)
	),
	'Submits a like/unlike on an entity based on user id and entity id',
	'POST',
	true,
	false
);

elgg_ws_expose_function(
	"like.count",
	"like_count",
	array(
		"guid" => array('type' => 'int', 'required' => true),
		"user" => array('type' => 'string', 'required' => false)
	),
	'Retreives a like count on an entity based on user id and entity id',
	'POST',
	true,
	false
);

function like_item( $id, $guid ){
	$user = ( strpos($id, '@') !== FALSE ) ? get_user_by_email($id)[0] : getUserFromID($id);

 	if( !$user )
		return "User was not found. Please try a different GUID, username, or email address";

	if( !$guid )
		return "GUID not found. Please enter a GUID";

	// Let's see if we can get a Wire Post with the specified GUID
	$entity = get_entity($guid);
	if( !$entity ) {
		return elgg_echo("likes:notfound");
	}

	$likes = elgg_get_annotations(array(
		'guid' => $guid,
		'annotation_owner_guid' => $user->guid,
		'annotation_name' => 'likes'
	));

	$liked = false;

	// check to see if the user has already liked the item
	if( !empty($likes) ){
		$like = $likes[0];
		if( $like && $like->canEdit() ){
			$like->delete();
			$data['message'] = elgg_echo("likes:deleted");
		}
	} else {
		$annotation_id = create_annotation($entity->guid, 'likes', "likes", "", $user->guid, $entity->access_id);
		$liked = true;

		// notify if poster wasn't owner
		if( $entity->owner_guid != $user->guid ){
			$owner = $entity->getOwnerEntity();

			$annotation = elgg_get_annotation_from_id($annotation_id);

			$title_str = $entity->getDisplayName();
			if( !$title_str ){
				$title_str = elgg_get_excerpt($entity->description);
			}

			$site = elgg_get_site_entity();

			$subject = elgg_echo('likes:notifications:subject', array(
					$user->name,
					$title_str
				),
				$owner->language
			);

			$body = elgg_echo('likes:notifications:body', array(
					$owner->name,
					$user->name,
					$title_str,
					$site->name,
					$entity->getURL(),
					$user->getURL()
				),
				$owner->language
			);

			notify_user(
				$entity->owner_guid,
				$user->guid,
				$subject,
				$body,
				array(
					'action' => 'create',
					'object' => $annotation,
				)
			);
		}

		$data['message'] = elgg_echo("likes:likes");
	}

	$likes = elgg_get_annotations(array(
		'guid' => $guid,
		'annotation_name' => 'likes'
	));
	$data['count'] = count($likes);

	$data['liked'] = $liked;

	return $data;
}

function like_count( $guid, $id ){
	if( !$guid )
		return "GUID not found. Please enter a GUID";

	// Let's see if we can get a Wire Post with the specified GUID
	$entity = get_entity($guid);
	if( !$entity ) {
		return elgg_echo("likes:notfound");
	}

	$likes = elgg_get_annotations(array(
		'guid' => $guid,
		'annotation_name' => 'likes'
	));
	$data['count'] = count($likes);

	if( $id ){
		$user = ( strpos($id, '@') !== FALSE ) ? get_user_by_email($id)[0] : getUserFromID($id);

		if( $user ){
			$likes = elgg_get_annotations(array(
				'guid' => $guid,
				'annotation_owner_guid' => $user->guid,
				'annotation_name' => 'likes'
			));
			$data['liked'] = count($likes) > 0;
		}
	}

	return $data;
}
