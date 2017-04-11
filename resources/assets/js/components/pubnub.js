import PubNub from 'pubnub';
export default {
    init() {
        window.pubnub = new PubNub({
		    subscribeKey: 'sub-c-7b32fe48-1dbe-11e7-894d-0619f8945a4f',
		    publishKey: 'pub-c-839610b5-f158-4b47-8d39-4902b61d7dd3'
		});
    },
    subscribe(){
    	pubnub.subscribe({ channels: ['ph1'] });
		pubnub.addListener({
		    message: function (message) {
		        console.log(message);
		    }
		})
    }
}