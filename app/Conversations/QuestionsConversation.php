<?php

namespace App\Conversations;

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use PhpParser\Node\Stmt\Else_;

class QuestionsConversation extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */
     protected $name,$age,$gender,$testresult;
    public function askName()
    {
        $question = Question::create("What is your name?")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason');

        return $this->ask($question, function (Answer $answer) {
           $this->name = $answer->getText();
           $this->askPermission();
        });
    }
    public function askPermission()
    {
        $this->say("Welcome, ".$this->name);
        $question = Question::create("The purpose of the Coronavirus Self-Checker is to help you make decisions about seeking appropriate
        medical care. This system is not intended for the diagnosis or treatment of disease, including COVID-19.
        This project was made possible through a partnership with the CDC Foundation and is enabled by
        Microsoft’s Azure platform. CDC’s collaboration with a non-federal organization does not imply an
        endorsement of any one particular service, product, or enterprise. ")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('I agree')->value('agree'),
                Button::create('I don\'t agree ')->value('disagree'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'agree') {
                   $this->askAge();
                } else {
                    $this->say("Please consent to use the Coronavirus Self-Checker. Refresh the page to start again.");
                }
            }
        });
    }


    public function askAge()
    {
        $question = Question::create("What is your age?")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason');

        return $this->ask($question, function (Answer $answer) {
           $this->age = $answer->getText();
           if (is_numeric($this->age)) {
                 if ($this->age<2) {
                     $this->say("This tool is intended for people 2 years or older. Please call the child’s medical provider, clinician
                     advice line, or telemedicine provider if your child is less than 2 years old and sick.");
                 }
                 if ($this->age>1 && $this->age<10) {
                    $this->say("Please ask your parent or guardian to help you complete these questions");
                }
                if ($this->age>9 && $this->age<13) {
                    $this->say("Please ask your parent or guardian to answer these questions with you");
                    $this->askGender();
                }
               
                 if ($this->age<18 && $this->age>12) {
                    $this->say("Ask a parent or guardian to assist you, or if taking by yourself, share these results with your
                    parent/guardian");
                    $this->askGender();
                 }
                 if($this->age>17 && $this->age<100){
                     $this->askGender();
                 }
                 else {
                    $this->say("Please enter a number for your age. Or refresh page ");
                    $this->askAge();
                 }
                 
           } else {
               $this->say("Please enter a number for your age. Or refresh page ");
               $this->askAge();
           }
           

           //$this->askPermission();
        });
    }


    public function askGender()
    {
        $question = Question::create("What is your (their) gender?")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('MALE')->value('male'),
                Button::create('FEMALE')->value('female'),
                Button::create('OTHERS')->value('OTHERS'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $gender =$answer->getValue();
                $this->say($gender);
                $this->askSymptoms();
                
            }
        });
    }

    public function askSymptoms()
    {

    if ($this->age>17) {
           $question = Question::create("Do you (they) have any of these life-threatening symptoms? \r\n
           Bluish lips or face\r\n
           Severe and constant pain or pressure in the chest\r\n
           Extreme difficulty breathing (such as gasping for air, being unable to talk without catching
           your (their) breath, severe wheezing, nostrils flaring)\r\n
           New disorientation (acting confused)\r\n
           Unconscious or very difficult to wake up\r\n
           Slurred speech or difficulty speaking (new or worsening)\r\n
           New or worsening seizures\r\n
           Signs of low blood pressure (too weak to stand, dizziness, lightheaded, feeling cold, pale,
           clammy skin)\r\n
           Dehydration (dry lips and mouth, not urinating much, sunken eyes)\n

           ")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('YES')->value('yes'),
                Button::create('NO')->value('no'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'yes') {
                    $this->say("Based on your symptoms, you may need urgent medical care. Please call 192 or go to the
                     nearest emergency department.
                     Tell the 911 operator or emergency staff if you have had contact with someone with COVID-19.");
                    
                 } else {
                     $this->isSick();
                 }
                
            }
        });
    }

       

    if ($this->age<18) {
        $question = Question::create("Do you (they) have any of these life-threatening symptoms? \n
        Bluish lips or face\n
        Severe and constant pain or pressure in the chest\n
        Extreme difficulty breathing (such as gasping for air, being unable to walk or talk without
        catching your (their) breath, severe wheezing, nostrils flaring, grunting, or using extra
        muscles around the chest to help breathe)\n
        Disoriented (acting confused or very irritable)\n
        Unconscious or very difficult to wake up\n
        New or worsening seizures\n
        Signs of low blood pressure (too weak to stand, dizziness, lightheaded, feeling cold, pale,
        clammy skin)\n
        Dehydration (dry lips and mouth, not urinating much, sunken eyes)\n
        Refusing to drink liquids\n
        Frequent vomiting \n

        ")
         ->fallback('Unable to ask question')
         ->callbackId('ask_reason')
         ->addButtons([
             Button::create('YES')->value('yes'),
             Button::create('NO')->value('no'),
         ]);

     return $this->ask($question, function (Answer $answer) {
         if ($answer->isInteractiveMessageReply()) {
             if ($answer->getValue() === 'yes') {
                 $this->say("Based on your symptoms, you may need urgent medical care. Please call 192 or go to the
                  nearest emergency department.
                  Tell the 911 operator or emergency staff if you have had contact with someone with COVID-19.");
                 
              } else {
                  $this->isSick();
              }
             
         }
     });
 }
 
    }

    public function isSick()
    {
        $question = Question::create("Are you (they) feeling sick?")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')->addButtons([
                Button::create('YES')->value('yes'),
                Button::create('NO')->value('no'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'yes') {
                   $this->isPositive();
                } else {
                    $this->say("Watch for COVID-19 symptoms. If you (they) develop any of these symptoms or if you (they) start to
                    feel worse, call your (their) medical provider, clinician advice line, or telemedicine provider.
                    Here are some steps that may help you (them) feel better:
                    • Stay at home and rest.
                    • Drink plenty of water and other clear liquids to prevent fluid loss (dehydration).
                    • Cover your coughs and sneezes with a tissue
                    • Wash your hands often with soap and water.");
                    $this->say("If you start to feel sick, tell a medical provider in the care center, nursing home, or shelter
                    where you live.");
                }
            }
        });
    }

    public function isPositive()
    {
        $question = Question::create("In the last 10 days, have you (they) tested positive for coronavirus?")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')->addButtons([
                Button::create('Yes, tested positive')->value('positive'),
                Button::create('No, tested negative')->value('negative'),
                Button::create('No, waiting for results ')->value('pending'),
                Button::create('No, not tested ')->value('no'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->testresult = $answer->getValue();
            }
            $this->symptoms();
        });
    }


    public function symptoms()
    {
        $question = Question::create("Would you say your (their) symptoms are mild, moderate, or severe?")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')->addButtons([
                Button::create('Mild: Can perform activities of daily living (such as cook, shower, eat and drink) without
                feeling short of breath.')->value('mild'),
                Button::create('Moderate: Has difficulty breathing (or shortness of breath) and can only perform limited
                activities of daily living such as eat and shower.')->value('moderate'),
                Button::create('Severe: Has shortness of breath and/or rapid breathing with severely limited ability or
                inability to perform activities of daily living')->value('severe'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'severe'  || $answer->getValue()==="moderate") {
                  $this->stayWhere();
                } else {
                    $this->say("Please consent to use the Coronavirus Self-Checker. Refresh the page to start again.");
                }
            }
        });
    }


    public function stayWhere()
    {
        $question = Question::create("Do you (they) live in a long-term care facility, nursing home, or homeless shelter?")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')->addButtons([
                Button::create('YES')->value('yes'),
                Button::create('NO')->value('no'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'yes') {
                    switch ($this->testresult) {
                        case 'positive':
                            $this->say("Tell a caregiver in your (their) facility that you (they) are sick and need to see a medical provider as
                            soon as possible. Living in a long-term care facility or nursing home may put you (them) at a higher
                            risk for severe illness.
                            Help protect others from getting sick:
                            • Stay in your room as much as possible except to get medical care.
                            • Cover your coughs and sneezes with a tissue .
                            • Wash your hands often with soap and water.
                            • Avoid close contact with other people. Stay at least 6 feet away from other people.
                            • Wear a mask when around others.
                            • Clean and disinfect frequently touched surfaces in your room.
                            • Monitor your health and notify a medical provider if you think you are getting sicker.");
                            $this->say("Stay home and away from others until
                            • it has been 10 days since symptoms first appeared and
                            • 24 hours with no fever without the use of fever-reducing medications and
                            • other symptoms of COVID-19 are improving* (*Loss of taste and smell may persist for weeks or
                            months after recovery and need not delay the end of isolation)
                            • Please inform your (their) close contacts that they have been potentially exposed to SARS-CoV-2.
                            CDC recommends that all close contacts people with confirmed or probable COVID should:
                            o get tested and
                            o quarantine for 14 days from the day of their last exposure. You (they) may also receive a call
                            from a contact tracing professional – see this video for more information.
                            • Get rest and stay hydrated. Take over-the-counter medicines, such as acetaminophen, if needed
                            to help you (them) feel better.
                            • Separate yourself (them) from other people. As much as possible, stay in a specific room and
                            away from other people and pets in your (their) home.");
                            break;
                        case 'negative':
                             $this->say("Tell a caregiver in your (their) facility that you (they) are sick and need to see a medical provider as
                             soon as possible. Living in a long-term care facility or nursing home may put you (them) at a higher
                             risk for severe illness.
                             Help protect others from getting sick:
                             • Stay in your room as much as possible except to get medical care.
                             • Cover your coughs and sneezes with a tissue .
                             • Wash your hands often with soap and water.
                             • Avoid close contact with other people. Stay at least 6 feet away from other people.
                             • Wear a mask when around others.
                             • Clean and disinfect frequently touched surfaces in your room.
                             • Monitor your health and notify a medical provider if you think you are getting sicker.");
                             $this->say("It is possible that you (they) were very early in your (their) infection when your (their) sample was
                             collected and that you (they) could test positive later.
                             • If your (their) symptoms worsen after testing negative, please contact your (their) healthcare
                             provider.");
                             $this->say("CDC recommends that all close contacts of people with confirmed COVID-19 should2:
                             o quarantine for 14 days from the day of their last exposure. You (they) may also receive a call
                             from a contact tracing professional – see this video for more information.");
                             break;
                        case 'pending':
                                 $this->say("Tell a caregiver in your (their) facility that you (they) are sick and need to see a medical provider as
                                 soon as possible. Living in a long-term care facility or nursing home may put you (them) at a higher
                                 risk for severe illness.
                                 Help protect others from getting sick:
                                 • Stay in your room as much as possible except to get medical care.
                                 • Cover your coughs and sneezes with a tissue .
                                 • Wash your hands often with soap and water.
                                 • Avoid close contact with other people. Stay at least 6 feet away from other people.
                                 • Wear a mask when around others.
                                 • Clean and disinfect frequently touched surfaces in your room.
                                 • Monitor your health and notify a medical provider if you think you are getting sicker.");
                                 $this->say("If you (they) do get tested, you (they) should isolate at home pending test results and follow the
                                 advice of your (their) health care provider or a public health professional.");
                                 $this->say("CDC recommends that all close contacts of people with confirmed COVID-19 should2:
                                 o quarantine for 14 days from the day of their last exposure. You (they) may also receive a call
                                 from a contact tracing professional – see this video for more information.");
                             break;
                        case 'no':
                            $this->say("Tell a caregiver in your (their) facility that you (they) are sick and need to see a medical provider as
                            soon as possible. Living in a long-term care facility or nursing home may put you (them) at a higher
                            risk for severe illness.
                            Help protect others from getting sick:
                            • Stay in your room as much as possible except to get medical care.
                            • Cover your coughs and sneezes with a tissue .
                            • Wash your hands often with soap and water.
                            • Avoid close contact with other people. Stay at least 6 feet away from other people.
                            • Wear a mask when around others.
                            • Clean and disinfect frequently touched surfaces in your room.
                            • Monitor your health and notify a medical provider if you think you are getting sicker.");
                            $this->say("Stay home and away from others until
                            o it has been 10 days since symptoms first appeared and
                            o 24 hours with no fever without the use of fever-reducing medications and
                            o other symptoms of COVID-19 are improving* (*Loss of taste and smell may persist for weeks or
                            months after recovery and need not delay the end of isolation)");
                            $this->say("CDC recommends that all close contacts of people with confirmed COVID-19 should2:
                            o quarantine for 14 days from the day of their last exposure. You (they) may also receive a call
                            from a contact tracing professional – see this video for more information.");
                             break;
                        
                        default:
                            # code...
                            break;
                    }
                } else {
                    $this->volunteer();
                }
            }
        });
    }


    public function volunteer()
    {
        $question = Question::create("In the last two weeks, have you (they) worked or volunteered in a healthcare facility or as a first
        responder? Healthcare facilities include a hospital, medical or dental clinic, long-term care facility,
        or nursing home.")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')->addButtons([
                Button::create('YES')->value('yes'),
                Button::create('NO')->value('no'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'yes') {
                    switch ($this->testresult) {
                        case 'positive':
                            $this->say("Sorry you are (or your child is) not feeling well. Your symptoms may be related to COVID-19.
                            • Stay home (keep them home) except to get medical care.
                            o Do not go to work, school, or public areas including grocery stores, pharmacies, or
                            restaurants. Consider delivery options for food and medicine.
                            o Do not use public transportation or ride sharing.
                            • Cover your coughs and sneezes with a tissue.
                            • Wash your hands often with soap and water.
                            • Avoid close contact with other people. Stay at least 6 feet away from other people.
                            • Wear a mask when around others.
                            • If you (they) feel worse, and you (they) think it is an emergency, call 911 or seek medical care
                            immediately.
                            • Learn how to take care of yourself or someone else who is sick.
                            • Take steps to help protect others from getting sick.");
                            $this->say("Tell the occupational health provider (or supervisor) in your workplace that you’re feeling sick as
                            soon as possible.
                            • Follow any additional guidelines from public health officials");
                            break;
                        case 'negative':
                            $this->say("Sorry you are (or your child is) not feeling well. Your symptoms may be related to COVID-19.
                            • Stay home (keep them home) except to get medical care.
                            o Do not go to work, school, or public areas including grocery stores, pharmacies, or
                            restaurants. Consider delivery options for food and medicine.
                            o Do not use public transportation or ride sharing.
                            • Cover your coughs and sneezes with a tissue.
                            • Wash your hands often with soap and water.
                            • Avoid close contact with other people. Stay at least 6 feet away from other people.
                            • Wear a mask when around others.
                            • If you (they) feel worse, and you (they) think it is an emergency, call 911 or seek medical care
                            immediately.
                            • Learn how to take care of yourself or someone else who is sick.
                            • Take steps to help protect others from getting sick.");
                            $this->say("Tell the occupational health provider (or supervisor) in your workplace that you’re feeling sick as
                            soon as possible.
                            • Follow any additional guidelines from public health officials");
                             $this->say("It is possible that you (they) were very early in your (their) infection when your (their) sample was
                             collected and that you (they) could test positive later.
                             • If your (their) symptoms worsen after testing negative, please contact your (their) healthcare
                             provider.");
                             $this->say("CDC recommends that all close contacts of people with confirmed COVID-19 should2:
                             o quarantine for 14 days from the day of their last exposure. You (they) may also receive a call
                             from a contact tracing professional – see this video for more information.");
                             break;
                        case 'pending':
                            $this->say("Sorry you are (or your child is) not feeling well. Your symptoms may be related to COVID-19.
                            • Stay home (keep them home) except to get medical care.
                            o Do not go to work, school, or public areas including grocery stores, pharmacies, or
                            restaurants. Consider delivery options for food and medicine.
                            o Do not use public transportation or ride sharing.
                            • Cover your coughs and sneezes with a tissue.
                            • Wash your hands often with soap and water.
                            • Avoid close contact with other people. Stay at least 6 feet away from other people.
                            • Wear a mask when around others.
                            • If you (they) feel worse, and you (they) think it is an emergency, call 911 or seek medical care
                            immediately.
                            • Learn how to take care of yourself or someone else who is sick.
                            • Take steps to help protect others from getting sick.");
                            $this->say("Tell the occupational health provider (or supervisor) in your workplace that you’re feeling sick as
                            soon as possible.
                            • Follow any additional guidelines from public health officials");
                                 $this->say("If you (they) do get tested, you (they) should isolate at home pending test results and follow the
                                 advice of your (their) health care provider or a public health professional.");
                                 $this->say("CDC recommends that all close contacts of people with confirmed COVID-19 should2:
                                 o quarantine for 14 days from the day of their last exposure. You (they) may also receive a call
                                 from a contact tracing professional – see this video for more information.");
                            
                             break;
                        case 'no':
                            $this->say("Sorry you are (or your child is) not feeling well. Your symptoms may be related to COVID-19.
                            • Stay home (keep them home) except to get medical care.
                            o Do not go to work, school, or public areas including grocery stores, pharmacies, or
                            restaurants. Consider delivery options for food and medicine.
                            o Do not use public transportation or ride sharing.
                            • Cover your coughs and sneezes with a tissue.
                            • Wash your hands often with soap and water.
                            • Avoid close contact with other people. Stay at least 6 feet away from other people.
                            • Wear a mask when around others.
                            • If you (they) feel worse, and you (they) think it is an emergency, call 911 or seek medical care
                            immediately.
                            • Learn how to take care of yourself or someone else who is sick.
                            • Take steps to help protect others from getting sick.");
                            $this->say("Tell the occupational health provider (or supervisor) in your workplace that you’re feeling sick as
                            soon as possible.
                            • Follow any additional guidelines from public health officials");
                            $this->say("CDC recommends that all close contacts of people with confirmed COVID-19 should2:
                            o quarantine for 14 days from the day of their last exposure. You (they) may also receive a call
                            from a contact tracing professional – see this video for more information.");
                            $this->say("Stay home and away from others until
                            o it has been 10 days since symptoms first appeared and
                            o 24 hours with no fever without the use of fever-reducing medications and
                            o other symptoms of COVID-19 are improving* (*Loss of taste and smell may persist for weeks or
                            months after recovery and need not delay the end of isolation)");
                             break;
                        
                        default:
                            # code...
                            break;
                    }
                } else {
                    $this->say("CDC recommends that all close contacts of people with confirmed COVID-19 should2:
                            o quarantine for 14 days from the day of their last exposure. You (they) may also receive a call
                            from a contact tracing professional – see this video for more information.");
                            $this->say("Stay home and away from others until
                            o it has been 10 days since symptoms first appeared and
                            o 24 hours with no fever without the use of fever-reducing medications and
                            o other symptoms of COVID-19 are improving* (*Loss of taste and smell may persist for weeks or
                            months after recovery and need not delay the end of isolation)");
                }
            }
        });
    }
    


    public function run()
    {
        $this->askName();
    }
}


































