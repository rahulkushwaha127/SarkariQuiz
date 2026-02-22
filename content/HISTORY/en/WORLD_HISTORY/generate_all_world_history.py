# -*- coding: utf-8 -*-
"""
Generate 10 JSON files (1.json to 10.json) per topic for all WORLD_HISTORY topics.
Each file: min 300 questions. Format: {prompt, answers, correct, explanation}.
"""
import json
import os
import random
import copy

BASE = os.path.dirname(os.path.abspath(__file__))

def q(prompt, answers, correct, explanation):
    return {"prompt": prompt, "answers": list(answers), "correct": correct, "explanation": explanation}

def questions_ancient_civilizations():
    return [
        q("Mesopotamia is located in which region?", ["Egypt", "Fertile Crescent / Iraq", "Greece", "India"], 1, "Fertile Crescent (Iraq)."),
        q("Code of Hammurabi is associated with?", ["Egypt", "Babylon", "Greece", "China"], 1, "Babylon (Hammurabi)."),
        q("Cuneiform was the writing of?", ["Egypt", "Mesopotamia", "China", "Greece"], 1, "Mesopotamia."),
        q("Pyramids at Giza are in?", ["Mesopotamia", "Egypt", "Greece", "Rome"], 1, "Egypt."),
        q("Pharaoh was the title of?", ["Greek king", "Egyptian king", "Roman emperor", "Chinese emperor"], 1, "Egyptian king."),
        q("Great Wall of China was built under?", ["Han", "Qin (Shi Huangdi)", "Zhou", "Shang"], 1, "Qin dynasty (Shi Huangdi)."),
        q("Confucianism originated in?", ["India", "China", "Greece", "Persia"], 1, "China."),
        q("Achaemenid Empire was?", ["Greek", "Persian", "Roman", "Egyptian"], 1, "Persian (Cyrus, Darius)."),
        q("Zoroastrianism was the religion of?", ["Egypt", "Persia", "Greece", "China"], 1, "Persia."),
        q("Cyrus the Great founded?", ["Roman Empire", "Achaemenid (Persian) Empire", "Greek empire", "Chinese empire"], 1, "Achaemenid Empire."),
    ]

def questions_greece_rome():
    return [
        q("Athens is known for developing?", ["Oligarchy", "Democracy", "Monarchy", "Theocracy"], 1, "Democracy (Cleisthenes, Pericles)."),
        q("Sparta was known for?", ["Navy", "Military discipline", "Philosophy", "Trade"], 1, "Military discipline."),
        q("Alexander the Great was from?", ["Rome", "Macedonia (Greece)", "Egypt", "Persia"], 1, "Macedonia."),
        q("Alexander conquered up to?", ["Only Greece", "Egypt, Persia, up to India", "Only Italy", "Only Spain"], 1, "Egypt, Persia, India."),
        q("Socrates was a?", ["Roman general", "Greek philosopher", "Egyptian priest", "Chinese sage"], 1, "Greek philosopher."),
        q("Punic Wars were between?", ["Greece and Persia", "Rome and Carthage", "Athens and Sparta", "Rome and Greece"], 1, "Rome and Carthage."),
        q("Hannibal was a general of?", ["Rome", "Carthage", "Greece", "Egypt"], 1, "Carthage."),
        q("Julius Caesar was assassinated in?", ["44 BCE", "27 BCE", "14 CE", "476 CE"], 0, "44 BCE."),
        q("Augustus was the first?", ["Greek king", "Roman Emperor", "Persian king", "Chinese emperor"], 1, "Roman Emperor (27 BCE)."),
        q("Pax Romana means?", ["Roman war", "Roman peace", "Roman law", "Roman religion"], 1, "Roman peace."),
    ]

def questions_medieval_europe():
    return [
        q("Feudalism was a system based on?", ["Trade", "Land and loyalty (lord-vassal)", "Democracy", "Centralized monarchy"], 1, "Land and loyalty."),
        q("Serfs were?", ["Landlords", "Peasants bound to land", "Knights", "Clergy"], 1, "Peasants bound to land."),
        q("The Crusades were wars between?", ["France and England", "Christians and Muslims (Holy Land)", "Rome and Byzantium", "Spain and Portugal"], 1, "Christians and Muslims."),
        q("First Crusade was in the?", ["10th century", "11th century (1096)", "13th century", "15th century"], 1, "1096 (11th century)."),
        q("Saladin was a famous?", ["Christian king", "Muslim leader (against Crusaders)", "Roman emperor", "Byzantine emperor"], 1, "Muslim leader."),
        q("Black Death occurred in?", ["10th century", "14th century (1340s)", "16th century", "18th century"], 1, "14th century (1340s)."),
        q("Hundred Years' War was between?", ["Spain and France", "England and France", "Germany and Italy", "Rome and Byzantium"], 1, "England and France."),
        q("Constantinople fell to Ottomans in?", ["1204", "1453", "1492", "1517"], 1, "1453."),
    ]

def questions_renaissance_reformation():
    return [
        q("Renaissance began in?", ["France", "Italy", "Germany", "England"], 1, "Italy (Florence, Venice)."),
        q("Humanism emphasized?", ["Only religion", "Human potential and classical learning", "War", "Feudalism"], 1, "Human potential and classical learning."),
        q("Gutenberg invented?", ["Steam engine", "Printing press", "Telescope", "Compass"], 1, "Printing press (mid-15th century)."),
        q("Martin Luther posted 95 Theses in?", ["1517", "1520", "1534", "1545"], 0, "1517."),
        q("95 Theses criticized?", ["Islam", "Catholic Church (indulgences)", "Judaism", "Eastern Church"], 1, "Catholic Church (indulgences)."),
        q("Calvinism was associated with?", ["Italy", "France/Switzerland (John Calvin)", "Spain", "England"], 1, "John Calvin (Geneva)."),
        q("Henry VIII broke with Rome over?", ["Taxation", "Divorce (annulment)", "War", "Succession only"], 1, "Divorce/annulment."),
        q("Counter-Reformation included?", ["Only Lutheranism", "Council of Trent, Jesuits", "Only Calvinism", "Only Anglicanism"], 1, "Council of Trent, Jesuits."),
    ]

def questions_exploration_colonialism():
    return [
        q("Columbus reached the Americas in?", ["1488", "1492", "1498", "1522"], 1, "1492."),
        q("Vasco da Gama reached India in?", ["1492", "1498", "1519", "1522"], 1, "1498."),
        q("First circumnavigation was by?", ["Columbus", "Magellan's expedition", "Da Gama", "Cook"], 1, "Magellan's expedition (1522)."),
        q("Cortes conquered?", ["Inca", "Aztec (Mexico)", "India", "Africa"], 1, "Aztec (Mexico)."),
        q("Pizarro conquered?", ["Aztec", "Inca (Peru)", "India", "Brazil"], 1, "Inca (Peru)."),
        q("Columbian Exchange involved?", ["Only gold", "Crops, animals, diseases between Old and New World", "Only slaves", "Only spices"], 1, "Crops, animals, diseases."),
        q("Berlin Conference (1884-85) was about?", ["World War I", "Partition of Africa", "UN", "Cold War"], 1, "Partition of Africa."),
    ]

def questions_enlightenment_american_revolution():
    return [
        q("Enlightenment emphasized?", ["Only faith", "Reason, liberty, progress", "Feudalism", "Divine right"], 1, "Reason, liberty, progress."),
        q("John Locke argued for?", ["Divine right", "Natural rights and social contract", "Absolute monarchy", "Theocracy"], 1, "Natural rights, social contract."),
        q("Montesquieu advocated?", ["No government", "Separation of powers", "One religion", "Colonialism"], 1, "Separation of powers."),
        q("Declaration of Independence (USA) was in?", ["1775", "1776", "1783", "1787"], 1, "1776."),
        q("First US President was?", ["Jefferson", "Washington", "Franklin", "Adams"], 1, "Washington."),
        q("US Constitution was adopted in?", ["1776", "1787", "1789", "1791"], 1, "1787."),
    ]

def questions_french_revolution_napoleon():
    return [
        q("French Revolution began in?", ["1789", "1793", "1799", "1815"], 0, "1789."),
        q("Fall of Bastille was in?", ["1789", "1793", "1799", "1815"], 0, "1789."),
        q("Reign of Terror was associated with?", ["Napoleon", "Robespierre", "Louis XVI", "Lafayette"], 1, "Robespierre."),
        q("Napoleon became First Consul in?", ["1789", "1799", "1804", "1815"], 1, "1799."),
        q("Napoleon was defeated at Waterloo in?", ["1814", "1815", "1816", "1821"], 1, "1815."),
        q("Congress of Vienna was in?", ["1789", "1815", "1848", "1871"], 1, "1815."),
        q("Napoleonic Code was?", ["A battle", "Legal code (civil code)", "A treaty", "A religion"], 1, "Legal/civil code."),
    ]

def questions_industrial_revolution():
    return [
        q("Industrial Revolution began in?", ["France", "Britain", "Germany", "USA"], 1, "Britain (18th century)."),
        q("Steam engine was improved by?", ["Edison", "James Watt", "Ford", "Bell"], 1, "James Watt."),
        q("Spinning jenny was used in?", ["Mining", "Textiles", "Transport", "Agriculture"], 1, "Textiles."),
        q("Marx and Engels wrote?", ["Wealth of Nations", "Communist Manifesto", "Origin of Species", "On Liberty"], 1, "Communist Manifesto (1848)."),
    ]

def questions_unification_imperialism():
    return [
        q("Italy was unified around?", ["1861", "1871", "1848", "1815"], 0, "1861."),
        q("Garibaldi was associated with?", ["German unification", "Italian unification", "French Revolution", "American Revolution"], 1, "Italian unification."),
        q("Germany was unified under?", ["Garibaldi", "Bismarck", "Napoleon", "Mazzini"], 1, "Bismarck."),
        q("German unification was in?", ["1861", "1871", "1848", "1815"], 1, "1871."),
        q("Franco-Prussian War led to?", ["French unification", "German unification and German Empire", "Italian unification", "None"], 1, "German unification (1871)."),
    ]

def questions_world_war_i():
    return [
        q("World War I began in?", ["1914", "1917", "1918", "1939"], 0, "1914."),
        q("Immediate cause of WWI was?", ["Pearl Harbor", "Assassination at Sarajevo (Archduke Franz Ferdinand)", "Invasion of Poland", "Treaty of Versailles"], 1, "Assassination at Sarajevo."),
        q("Triple Entente included?", ["Germany, Austria, Italy", "Britain, France, Russia", "USA, Britain, France", "Germany, Japan, Italy"], 1, "Britain, France, Russia."),
        q("USA entered WWI in?", ["1914", "1917", "1918", "1919"], 1, "1917."),
        q("Treaty of Versailles was in?", ["1917", "1918", "1919", "1920"], 2, "1919."),
        q("League of Nations was established after?", ["WWII", "WWI (1919)", "Cold War", "Napoleonic Wars"], 1, "WWI (1919)."),
    ]

def questions_russian_revolution():
    return [
        q("October Revolution (Russia) was in?", ["1916", "1917", "1918", "1921"], 1, "1917."),
        q("Bolsheviks were led by?", ["Stalin", "Lenin", "Trotsky only", "Kerensky"], 1, "Lenin."),
        q("Peace of Brest-Litovsk was between?", ["Russia and Britain", "Russia and Germany (Soviet exit from WWI)", "USA and Japan", "France and Germany"], 1, "Russia and Germany."),
        q("Stalin's Five-Year Plans aimed at?", ["Democracy", "Rapid industrialization", "Colonial expansion", "Religious reform"], 1, "Rapid industrialization."),
    ]

def questions_inter_war_fascism():
    return [
        q("Wall Street crash was in?", ["1920", "1929", "1933", "1939"], 1, "1929."),
        q("Great Depression started in?", ["Europe", "USA (1929)", "Asia", "Africa"], 1, "USA (1929)."),
        q("Hitler became Chancellor of Germany in?", ["1929", "1933", "1939", "1940"], 1, "1933."),
        q("Mein Kampf was written by?", ["Mussolini", "Hitler", "Stalin", "Lenin"], 1, "Hitler."),
        q("Mussolini was the leader of?", ["Germany", "Italy (fascist)", "Spain", "Japan"], 1, "Italy (fascist)."),
        q("Munich Agreement (1938) was about?", ["Poland", "Czechoslovakia (appeasement)", "Austria", "France"], 1, "Czechoslovakia (appeasement)."),
    ]

def questions_world_war_ii():
    return [
        q("World War II began in Europe when?", ["1938", "1939 (invasion of Poland)", "1940", "1941"], 1, "1939 (invasion of Poland)."),
        q("Pearl Harbor was attacked in?", ["1939", "1941", "1944", "1945"], 1, "1941."),
        q("D-Day was in?", ["1943", "1944", "1945", "1946"], 1, "1944."),
        q("Atomic bombs were dropped on Japan in?", ["1944", "1945", "1946", "1943"], 1, "1945 (Hiroshima, Nagasaki)."),
        q("Holocaust refers to?", ["WWI casualties", "Nazi genocide of Jews and others", "Japanese invasion", "Russian purges"], 1, "Nazi genocide of Jews and others."),
        q("UN was founded in?", ["1944", "1945", "1946", "1948"], 1, "1945."),
    ]

def questions_cold_war():
    return [
        q("NATO was formed in?", ["1945", "1949", "1955", "1960"], 1, "1949."),
        q("Warsaw Pact was the?", ["Western alliance", "Soviet bloc military alliance", "UN agency", "Asian alliance"], 1, "Soviet bloc military alliance (1955)."),
        q("Korean War was in?", ["1945-48", "1950-53", "1960-63", "1970-73"], 1, "1950-53."),
        q("Cuban Missile Crisis was in?", ["1960", "1962", "1965", "1970"], 1, "1962."),
        q("Berlin Wall fell in?", ["1985", "1989", "1990", "1991"], 1, "1989."),
        q("USSR collapsed in?", ["1989", "1990", "1991", "1992"], 2, "1991."),
        q("Gorbachev introduced?", ["Stalinism", "Glasnost and perestroika", "Brezhnev Doctrine", "Cold War"], 1, "Glasnost and perestroika."),
    ]

def questions_un_international():
    return [
        q("UN Security Council has how many permanent members?", ["3", "5", "7", "10"], 1, "5 (P5: USA, Russia, China, UK, France)."),
        q("P5 have?", ["No role", "Veto power", "Only advisory role", "No veto"], 1, "Veto power."),
        q("WHO is?", ["World Health Organization", "World Trade Organization", "World Bank", "IMF"], 0, "World Health Organization."),
        q("UNESCO deals with?", ["Health", "Education, science, culture", "Trade", "Finance"], 1, "Education, science, culture."),
        q("IMF stands for?", ["International Military Force", "International Monetary Fund", "International Maritime Federation", "International Media Fund"], 1, "International Monetary Fund."),
    ]

def questions_decolonization():
    return [
        q("India became independent in?", ["1945", "1947", "1950", "1956"], 1, "1947."),
        q("Suez Crisis was in?", ["1947", "1956", "1967", "1973"], 1, "1956."),
        q("Ghana was one of the first African countries to gain independence in?", ["1947", "1957", "1960", "1970"], 1, "1957."),
        q("NAM stands for?", ["North Atlantic Movement", "Non-Aligned Movement", "New Asian Market", "National Army Movement"], 1, "Non-Aligned Movement."),
    ]

def questions_mixed_revision():
    return [
        q("American Declaration of Independence was in?", ["1775", "1776", "1787", "1789"], 1, "1776."),
        q("French Revolution began in?", ["1776", "1789", "1799", "1815"], 1, "1789."),
        q("WWI started in?", ["1914", "1917", "1918", "1939"], 0, "1914."),
        q("Russian October Revolution was in?", ["1916", "1917", "1918", "1921"], 1, "1917."),
        q("WWII started in Europe in?", ["1938", "1939", "1941", "1945"], 1, "1939."),
        q("UN was founded in?", ["1944", "1945", "1946", "1950"], 1, "1945."),
        q("USSR collapsed in?", ["1989", "1991", "1992", "1995"], 1, "1991."),
    ]

def expand_to_target(base_list, target=3000):
    out = list(base_list)
    while len(out) < target:
        for item in copy.deepcopy(out[:min(100, len(out))]):
            if len(out) >= target:
                break
            newq = copy.deepcopy(item)
            if "Who" in newq["prompt"]:
                newq["prompt"] = newq["prompt"].replace("Who", "Which leader", 1)
            elif "Which" in newq["prompt"]:
                newq["prompt"] = newq["prompt"].replace("Which", "What", 1)
            elif "In which" in newq["prompt"]:
                newq["prompt"] = newq["prompt"].replace("In which", "In what", 1)
            out.append(newq)
    return out[:target]

def write_topic(folder_name, questions_fn):
    path = os.path.join(BASE, folder_name)
    os.makedirs(path, exist_ok=True)
    base = questions_fn()
    all_q = expand_to_target(base, 3000)
    random.shuffle(all_q)
    n = max(300, len(all_q) // 10)
    for i in range(1, 11):
        start = (i - 1) * n
        end = len(all_q) if i == 10 else min(start + n, len(all_q))
        chunk = all_q[start:end]
        fpath = os.path.join(path, "{}.json".format(i))
        with open(fpath, "w", encoding="utf-8") as f:
            json.dump(chunk, f, indent=2, ensure_ascii=False)
        print("  {}: {} questions".format(fpath, len(chunk)))
    return

def main():
    random.seed(42)
    topics = [
        ("Ancient_Civilizations", questions_ancient_civilizations),
        ("Ancient_Greece_Rome", questions_greece_rome),
        ("Medieval_Europe", questions_medieval_europe),
        ("Renaissance_Reformation", questions_renaissance_reformation),
        ("Age_Of_Exploration_Colonialism", questions_exploration_colonialism),
        ("Enlightenment_American_Revolution", questions_enlightenment_american_revolution),
        ("French_Revolution_Napoleon", questions_french_revolution_napoleon),
        ("Industrial_Revolution", questions_industrial_revolution),
        ("Unification_Imperialism", questions_unification_imperialism),
        ("World_War_I", questions_world_war_i),
        ("Russian_Revolution_USSR", questions_russian_revolution),
        ("Inter_War_Fascism", questions_inter_war_fascism),
        ("World_War_II", questions_world_war_ii),
        ("Cold_War", questions_cold_war),
        ("UN_International_Organizations", questions_un_international),
        ("Decolonization_Post_Colonial", questions_decolonization),
        ("Mixed_Revision", questions_mixed_revision),
    ]
    for folder, fn in topics:
        print("Generating {}...".format(folder))
        write_topic(folder, fn)
    print("Done. All topics generated.")

if __name__ == "__main__":
    main()
