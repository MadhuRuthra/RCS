Service List
1) login api,mobile_login
2) Logout api.mobile_logout
3) signup api,primary admin create on master based users
4) dashboard api.
5) add senderid - insert the db only.delete senderid
6) approve senderid - 
7) create template 
8) template list api 
9) compose message - 
10) compose message list
11) messenger response



URL : https://yourpostman.in/whatsapp_service/
login id : primary_admin,admin_1,admin_2,dept_head_1,waba_user_2
Password : Celeb@123#




  fs.createReadStream(receiver_nos_path)
                                                                                 // Read the CSV file from the stream
                                                                                 .pipe(csv({
                                                                                     headers: false
                                                                                 })) // Set headers to false since there are no column headers
                                                                                 .on('data', (row) => {
                                                                                     // Skip the first row (header)
                                                                                  /*   if (isFirstRow) {
                                                                                         isFirstRow = false;
                                                                                         return;
                                                                                     }*/
                                                                                     const firstColumnValue = row[0];
                                                                                     // Validate mobile number format
                                                                                     const isValidFormat = /^\d{12}$/.test(firstColumnValue) && firstColumnValue.startsWith('91') && /^[6-9]/.test(firstColumnValue.substring(2, 3));
                                                                                     // Check for duplicates
                                                                                     if (duplicateMobileNumbers.has(firstColumnValue)) {
                                                                                         invalid_mobile_numbers.push(firstColumnValue);
                                                                                     } else {
                                                                                         duplicateMobileNumbers.add(firstColumnValue);
                                                                                         if (isValidFormat) {
                                                                                             valid_mobile_numbers.push(firstColumnValue);
                                                                                             // Create a new array for each row
                                                                                             const otherColumnsArray = [];
                                                                                             let secondColumnValue;
                                                                                             for (let i = 1; i < Object.keys(row).length; i++) {
                                                                                                 // Skip processing if the mobile number is invalid
                                                                                                 if (!isValidFormat) {
                                                                                                     break;
                                                                                                 }

                                                                                                 if ((is_same_media_flag == "false") && (is_same_msg == "false") && (message_type == 'VIDEO') && (row[i] == row[1])) {
                                                                                                   console.log('here....')

                                                                                                     secondColumnValue = row[1];
                                                                                                     console.log(secondColumnValue);
                                                                                                     media_url.push(secondColumnValue.toString());

                                                                                                     i++;
                                                                                                 }
                                                                                                 otherColumnsArray.push(row[i]);
                                                                                             }
                                                                                             // Only push the otherColumnsArray if the mobile number is still valid
                                                                                             if (isValidFormat) {
                                                                                                 variable_values.push(otherColumnsArray);
                                                                                             }
                                                                                         } else {
                                                                                             invalid_mobile_numbers.push(firstColumnValue);
                                                                                         }
                                                                                     }
                                                                                 })
                                                                                 .on('error', (error) => {
                                                                                     console.error('Error:', error.message);
                                                                                 })
                                                                                 .on('end', async () => {
                                                                                     if (is_same_msg == "false" && is_same_media_flag == "false") {
                                                                                         media_url_csv = media_url;
                                                                                         console.log('Media URL:', media_url);
                                                                                         console.log(media_url.length);
                                                                                         console.log('Media URL:', media_url_csv + "media_url");
                                                                                         console.log(media_url_csv.length);
                                                                                     }
                                                                                     // Do something with the arrays
                                                                                     console.log('Valid Mobile Numbers:', valid_mobile_numbers);
                                                                                     console.log('Invalid Mobile Numbers:', invalid_mobile_numbers);
                                                                                     console.log('Other Columns Values:', variable_values);
                                                                                     console.log('Other Columns Values:', variable_values.length);