/*

 DIFINE NEW ENIGMA:
 Each Object is a different enigma.

 * = IS REQUIRED!

 var Enigma_1 = new Enigma(*passlength, *alphabet, *password, startanswer);

 *passlength = number of letters to find(positive integer)
 *alphabet   = (Array or String)

 String: returns string's inner values within ALL the password's
 letters as alphabet.
 Array:  returns first Array's element inner values as
 alphabet into first password's letter and so on...

 Each Alphabet element MUST be separated from others with _
 !BE CAREFULL,not identical elements of the same letter are
 allowed!
 !long element length IS allowed!

 Examples of alphabet parameter:

 "a_yy_3" -> ALL letters will rotate a,yy,3 values
 6       -> ALL letters will rotate 0 to 5 numbers
 Array("a_yy_3",6,"6") -> 1st letter will rotate a,yy,3 values
 2nd letter will rotate 0 to 5 numbers
 3rd letter will have "6" fixed value
 ! Array param must have as many elements as passlength value !

 *password = gives the correct answer for the enigma(String)
 MUST be set in the same form as Alphabet values
 Example of password parameter:

 var Enigma_1 = new Enigma(5, "a_yy_3", "yy_3_3_a_a");

 "yy_3_3_a_a" is a possible acceptable answer
 !REMEMBER is up to you create a possible answer, "yy_3_3_a_k"
 will make your enigma impossible to solve!

 startanswer = MUST be set in the same form as Alphabet values and password
 Example of startanswer parameter:

 var Enigma_1 = new Enigma(5, "a_yy_3", "yy_3_3_a_a","a_a_a_a_a");

 "a_a_a_a_a" will be starting answer,if startinganswer param is
 absent startinganser will be auto_created, in the example above
 would be the same ("a_a_a_a_a") because "a" is the first available
 alphabet value for each letter.

 !REMEMBER is up to you create a possible startanswer,
 "yy_3_3_a_k" will make your last letter impossible to rotate
 therefore your password impossible to be valid
 unless the password itself is invalid!

 CHANGING YOUR ENIGMA:
 Enigma_1.ChangeEnigma(*change, *repeat);
 self explaining example:
 Enigma_1.ChangeEnigma("2_1_3", "4_2_5");

 letter 2 will rotate by 4 elements
 letter 1 will rotate by 2 elements
 letter 3 will rotate by 5 elements

 NOTES:
 -ChangeEnigma method works also with numbers if you need to change only 1 letter
 Example:
 Enigma_1.ChangeEnigma(1,4);

 letter 1 will rotate by 4 elements

 -if your repeat is > then your changing letter elements the code will evaluate
 (repeat)%(Number changing letter elements)
 so not all changes will be executed.
 Example:
 var Enigma_1 = new Enigma(5, "a_yy_3", "yy_3_3_a_a","a_a_a_a_a");
 Enigma_1.ChangeEnigma(1,4);

 as you can see each letter has 3 elements a,yy,3.
 Changing 4 times a letter is the same for changing 1 time.
 Code evaluates 4%3 = 1 so it will execute like repeat=1

 !LOOK AT THE BOTTOM of the CODE for possible Enigma examples!
 */


// Object constructor
function Enigma(passlength, alphabet, password, startanswer) {

    this.PossibleAnswer = [];
    this.ActualAnswer = "";
    this.password = password;

    this.MakeEnigma(passlength, alphabet, startanswer);
}
;

// External Method for ActualAnswer evaluation
String.prototype.replaceAt = function(index, ActualAnswer, character) {
    var value = ActualAnswer.split("_");
    value[index] = character;
    return value.join("_");
};

// Library of shortcuts to often used significant values to pass as parameters:
// Library: "alf"      -> returns lowercase English alphabet
//          "ALF"      -> returns uppercase English alphabet
// writing an X number -> returns Alphabet 0 to X-1
//
// Avoiding UsualAphabests method usage will end up to make
// the code taking all passed values as strings.
Enigma.prototype.UsualAlphabets = function(alphabet, type)
{
    // Library of Array parameter
    if (type === "Array") {
        var y = alphabet.length;
        for (i = 0; i < y; i++) {
            if (alphabet[i] === "ALF")
            {
                this.alphabet[i] = "A_B_C_D_E_F_G_H_I_J_K_L_M_N_O_P_Q_R_S_T_U_V_W_X_Y_Z";
            }
            else if (alphabet[i] === "alf")
            {
                this.alphabet[i] = "a_b_c_d_e_f_g_h_i_j_k_l_m_n_o_p_q_r_s_t_u_v_w_x_y_z";
            }
            else if (typeof alphabet[i] === 'number' && (alphabet[i] % 1) === 0 && alphabet[i] >= 0) {

                var z = [];
                for (j = 0; j < alphabet[i]; j++) {

                    z[j] = j;
                }
                this.alphabet[i] = z.join("_");

            }
        }
    }
    // Library of String&Number parameters
    else if (type === "String_Number") {
        if (alphabet === "ALF")
        {
            this.alphabet = "A_B_C_D_E_F_G_H_I_J_K_L_M_N_O_P_Q_R_S_T_U_V_W_X_Y_Z";
        }
        else if (alphabet === "alf")
        {
            this.alphabet = "a_b_c_d_e_f_g_h_i_j_k_l_m_n_o_p_q_r_s_t_u_v_w_x_y_z";
        }
        else if (typeof alphabet === 'number' && (alphabet % 1) === 0 && alphabet >= 0) {

            var z = [];
            for (j = 0; j < alphabet; j++) {
                z[j] = j;
            }
            this.alphabet = z.join("_");

        }
    }

};

// MakeEnigma method Produces a nested Array tree structure
// into this.PossibleAnswer object property. you can access
// to each possible value for each possible letter of your
// Enigma object.It also fends to set starting answer for
// the same object.
Enigma.prototype.MakeEnigma = function(passlength, alphabet, startanswer) {
    this.passlength = passlength;
    this.alphabet = alphabet;
    this.startanswer = startanswer;


    // making this.PossibleAnswer if Array is passed as param.
    if (alphabet.constructor === Array) {

        this.UsualAlphabets(alphabet, "Array");

        for (k = 0; k < this.passlength; k++) {
            this.PossibleAnswer[k] = this.alphabet[k].split("_");
        }
    }
    // making this.PossibleAnswer if String or positive integer
    // is passed as param.
    if (alphabet.constructor === String || alphabet.constructor === Number) {

        this.UsualAlphabets(alphabet, "String_Number");

        for (k = 0; k < this.passlength; k++) {
            this.PossibleAnswer[k] = this.alphabet.split("_");

        }

    }
    //setting startanswer as string having for each letter
    //the first alphabet value available if startanswer
    //is not passed as parameter
    if (this.startanswer) {
        this.ActualAnswer = this.startanswer;
        console.log("Starting Answer = " + this.ActualAnswer);
    }
    else {
        var y = this.passlength;
        var z = [];
        for (i = 0; i < y; i++) {
            z[i] = this.PossibleAnswer[i][0];
        }
        this.ActualAnswer = z.join("_");
        console.log("Starting Answer = " + this.ActualAnswer);
    }
};
// returns true if answer is right,false otherwise
Enigma.prototype.Result = function() {
    return this.ActualAnswer === this.password;
};
//changes the ActualAnswer value rotating each possible alphabet element for
//each desired letter
Enigma.prototype.ChangeEnigma = function(change, repeat) {

    //Some Library to change ALL letters
    if (change === "ALL")
    {
        var k = [];
        for (j = 0; j < this.passlength; j++) {
            k[j] = j + 1;
        }
        change = k.join("_");
    }

    var allchange = String(change).split("_");
    var allrepeat = String(repeat).split("_");

    var minval = Math.min(allchange.length, allrepeat.length);

    for (k = 0; k < minval; k++) {

        change = allchange[k];
        repeat = allrepeat[k] % this.PossibleAnswer[k].length;

        if (this.ActualAnswer.length > 0)
        {

            var x = this.PossibleAnswer[change - 1];
            var y = x.length;
            var z = x[0];

            for (i = 0; i < repeat; i++) {
                this.indexArr = x.indexOf(this.ActualAnswer.split("_")[change - 1]);

                if (this.indexArr + 1 < y) {
                    this.ActualAnswer = this.ActualAnswer.replaceAt(change - 1, this.ActualAnswer, x[this.indexArr + 1]);
                }
                else {
                    this.ActualAnswer = this.ActualAnswer.replaceAt(change - 1, this.ActualAnswer, z);
                }
                console.log("Changing Answer = " + this.ActualAnswer);
            }
            console.log(" Midstep Answer = (" + this.ActualAnswer + ")");
        }

        else {
            console.log("Undefined Object Shape");
        }

    }
    console.log("New Answer = " + this.ActualAnswer);
};
/*
 calling a new Object is the same for making a new Enigma

 Call ChangeEnigma into external Js condition to keep this file separate
 from graphical implementation and change ActualAswer

 Call Result into external Js condition to keep this file separate
 from graphical implementation and verify if ActualAnswer is correct
 */

var Enigma_0 = new Enigma(3, 4, "3_2_3", "1_1_1");
Enigma_0.ChangeEnigma("ALL", "3_2");
Enigma_0.Result();

//var Enigma_1 = new Enigma(3, 4, "3_2_3", "1_1_1");
//Enigma_1.ChangeEnigma("1_2_3", "3_2_5");
//Enigma_1.Result();

//var Enigma_2 = new Enigma(3,Array(4,"alf","1_2"),"3_a_1", "1_c_1");
//Enigma_2.ChangeEnigma("1_2_3", "3_2_5");
//Enigma_2.Result();

//var Enigma_3 = new Enigma(3,10,"3_5_3", "1_4_1");
//Enigma_3.ChangeEnigma("1_1_3", "3_2_2");
//Enigma_3.Result();

//var Enigma_4 = new Enigma(3,Array("3_5_4","la_bella_casetta","ALF"),"3_la_H","5_la_H");
//Enigma_4.ChangeEnigma("1", 5);
//Enigma_4.ChangeEnigma(2, "5");
//Enigma_4.Result();

//var Enigma_5 = new Enigma(3,"5_aa_alf_2","5_alf_aa");
//Enigma_5.ChangeEnigma("1_4", 2); //4 ignored by code
//Enigma_5.ChangeEnigma(2, "5_6"); //6 ignored by code
//Enigma_5.Result();//NOTE:alf is called as element not as a shortcut
