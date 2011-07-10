/**
 * Javascript calls for gradebook screens
 */
jQuery('#user-grades').sparkline( 'html', { type: 'line', height: '30px', width: '100%' } );
jQuery('#user-progress').sparkline( 'html', { type: 'pie', height: '45px', width: '45px', sliceColors: ['#DDD', '#666'] } );