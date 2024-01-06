import {format} from 'date-fns';


const today = new Date();

console.log(JSONArray);

for (let i in JSONArray) {
    if (JSONArray.hasOwnProperty(i)) {
        //console.log('Now checking language ' + i);
        let language = JSONArray[i];
        for (let ii in language) {
            //console.log('Now checking key ' + ii);
            try {
                let result = format(today, language[ii]);
            } catch (err) {
                console.error('Language "' + i + '" could not parse key "' + ii + '" with value "' + language[ii] + '": ' + err);
            }
        }
    }
}