/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';
import { useSelect, select } from '@wordpress/data';
import { decodeEntities } from '@wordpress/html-entities';
import { store as coreDataStore } from '@wordpress/core-data';

/**
 * Post query hook.
 *
 * This hook provides a way to query posts by post type and search query.
 * It returns an object containing the posts and hasResolved properties.
 *
 * The posts property is an array of post objects, each containing the post's ID and title.
 * The hasResolved property is a boolean indicating whether the query has finished resolving.
 *
 * The hook takes three arguments:
 *
 * - `postType`: The post type to query.
 * - `selectedPostId`: The ID of the currently selected post. This is used to add a selected class to the corresponding option.
 * - `search`: The search query. If this is provided, the hook will add it as a search parameter to the query.
 *
 * Example usage: const { hasResolved, postOptions } = usePostsQuery( postType, selectedPostId, search );
 *
 * The hook uses the `wp/v2` REST API to query the posts. It passes the following query parameters:
 *
 * - `_fields=ids,title` to only retrieve the post's ID and title.
 * - `per_page=50` to limit the number of posts returned to 50.
 * - `search=<search query>` if the search argument is provided.
 *
 * The hook also uses the `useSelect` and `useMemo` hooks from `@wordpress/data` to cache the results of the query.
 * This means that the hook will only re-run the query if the search argument changes, and will return the cached result if it does not.
 *
 * @param {string} postType       - The post type to query.
 * @param {number} selectedPostId - The ID of the currently selected post.
 * @param {string} search         - The search query.
 * @return {Object} An object containing the posts and hasResolved properties.
 */
export function usePostsQuery( postType, selectedPostId, search ) {
	const { posts, hasResolved } = useSelect(
		( select ) => {
			const { getEntityRecords } = select( coreDataStore );
			const query = {
				_fields: 'id,title',
				per_page: 50,
			};

			if ( search && search.length > 0 ) {
				query.search = search;
			}

			return {
				posts: getEntityRecords( 'postType', postType, query ),
				hasResolved: select( coreDataStore ).hasFinishedResolution( 'getEntityRecords', [
					'postType',
					postType,
					query,
				] ),
			};
		},
		[ search, postType ]
	);

	const postOptions = useMemo( () => {
		const fetchedPosts = ( posts ?? [] ).map( ( post ) => {
			return {
				value: post.id,
				label: decodeEntities( post.title.raw ),
			};
		} );

		// Ensure the current post is in the options.
		const foundPost = fetchedPosts.findIndex( ( { value } ) => value === selectedPostId );

		// Retrieve the selected post if it is not in the fetched posts.
		if ( foundPost === -1 && selectedPostId ) {
			const post = select( coreDataStore ).getEntityRecord( 'postType', postType, selectedPostId );

			if ( post ) {
				fetchedPosts.push( {
					value: post.id,
					label: decodeEntities( post.title.raw ),
				} );
			}
		}

		return fetchedPosts;
	}, [ posts, selectedPostId, postType ] );

	return { hasResolved, postOptions };
}
