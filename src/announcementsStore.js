import { createReduxStore } from "@wordpress/data";
import apiFetch from '@wordpress/api-fetch';


const DEFAULT_STATE = {
	src: null
}

// Actions for the store
const actions = {
	setSrc(src){
		console.log("Set src:", src);
		return{
			type: 'SET_SRC',
			src
		};
	},

	fetchFromAPI(path){		
		return{
			type: 'FETCH_FROM_API',
			path
		};
	}
}

// Register the store
const announcementStore = createReduxStore('parish-announcements', {
	reducer(state = DEFAULT_STATE, action){		
		switch(action.type){			
			case 'SET_SRC':				
				return{					
					src: action.src
				};
		}
		return state;
	},
	actions: actions,
	selectors: {
		getSrc(state){
			console.log("getSrc", state);
			return state.src;
		}
	},
	
	controls: {
		FETCH_FROM_API(action){
			return apiFetch({path: action.path});
		}
	},
	
	resolvers:{
		*getSrc(){
			const path = '/parish-announcements/v1/active';
			const src = yield actions.fetchFromAPI(path);			
			return actions.setSrc(src.src);
		}
	}
})
export default announcementStore;	