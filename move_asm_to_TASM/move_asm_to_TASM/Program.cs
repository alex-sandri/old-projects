using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Threading;
using System.IO;
using System.Diagnostics;

namespace move_asm_to_TASM
{
    class Program
    {
        static void Main(string[] args)
        {
            Console.Title = "TriniTASM";

            string nome;
            string documentsPath;
            string assemblyPath;
            string localAppDataDOSBox = Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData) + "\\DOSBox\\dosbox-0.74-2.conf";
            string nomeCorto;

            string demoText = "DOSSEG\n\n.MODEL SMALL\n\n.STACK 100h\n\n.DATA\n\n.CODE\n\n\tMOV AX, @DATA\n\tMOV DS, AX\n\n\tMOV AH, 4Ch\n\tINT 21h\n\nEND\n\n;Created with TRINITASM";

            string TASMPath;

            string DOSBoxPath = "C:\\Program Files (x86)\\DOSBox-0.74-2\\DOSBox.exe";

            documentsPath = Environment.GetFolderPath(Environment.SpecialFolder.MyDocuments);

            string scelta;

            bool goBack = false;

            bool debug = false;

            string[] editLines = File.ReadAllLines(localAppDataDOSBox);
            string file = File.ReadAllText(localAppDataDOSBox);

            string[] backupFile = File.ReadAllLines(localAppDataDOSBox);

            int editLinesLenght = editLines.Length;

            file += Environment.NewLine + Environment.NewLine + Environment.NewLine + Environment.NewLine + Environment.NewLine + Environment.NewLine + Environment.NewLine + Environment.NewLine;

            File.WriteAllText(localAppDataDOSBox, file);

            editLines = File.ReadAllLines(localAppDataDOSBox);

            editLines[editLinesLenght + 1] = "mount C C:\\TASM";
            editLines[editLinesLenght + 2] = "C:";
            editLines[editLinesLenght + 3] = "cd TASM";

            File.WriteAllLines(localAppDataDOSBox, editLines);

            do
            {
                debug = false;

                do
                {
                    Console.Clear();

                    Console.WriteLine("Cosa vuoi fare?\n");

                    Console.WriteLine("1. Creare un file ed eseguirlo");
                    Console.WriteLine("2. Spostare ed eseguire un file già creato");
                    Console.WriteLine("3. Debug di un file già compilato");
                    Console.WriteLine("4. Accedere alla guida sull'Assembly 8086");
                    Console.WriteLine("5. Chiudere il programma (È consigliato utilizzare questo per uscire)");

                    Console.Write("\nDigita la tua scelta: ");
                    scelta = Console.ReadLine();

                    if (scelta == "3")
                    {
                        debug = true;
                    }
                    else if (scelta == "4")
                    {
                        Console.Clear();

                        Console.WriteLine("Guida Assembly 8086");

                        Console.WriteLine("\nINTERRUPT 21h: (AH <- ??h)\n");

                        Console.WriteLine("01h : Legge un carattere dal dispositivo standard di input (tastiera) STDIN             -> AL");
                        Console.WriteLine("02h : Scrive un carattere su dispositivo standard di output (video) STDOUT              <- DL");
                        Console.WriteLine("07h : Legge un carattere dal dispositivo standard di input (tastiera) STDIN senza eco   -> AL");
                        Console.WriteLine("09h : Scrive una stringa sul video STDOUT                                               <- DX");
                        Console.WriteLine("0Ah : Legge una stringa da tastiera STDIN                                               -> DX (inizio del buffer)");
                        Console.WriteLine("2Ah : Fornisce la data del sistema (anno, mese, giorno, giorno della settimana)         -> CX, DH, DL, AL");
                        Console.WriteLine("2Ch : Fornisce l’ora del sistema (ora, minuti, secondi, 1/100 di secondo                -> CH, CL, DH, DL");
                        Console.WriteLine("4Ch : Termina l’esecuzione di un “programma”                                            //");

                        Console.WriteLine("\n\nISTRUZIONI:\n");

                        Console.WriteLine("MOV         dest, sorg");
                        Console.WriteLine("LEA         dest, sorg");
                        Console.WriteLine("PUSH        sorg");
                        Console.WriteLine("POP         dest");
                        Console.WriteLine("ADD         dest, sorg");
                        Console.WriteLine("INC         dest");
                        Console.WriteLine("SUB         dest, sorg");
                        Console.WriteLine("DEC         dest");
                        Console.WriteLine("MUL         sorg (AX <- sorg * AL)");
                        Console.WriteLine("DIV         sorg (AL <- AX / sorg) (AH <- AX % sorg)");
                        Console.WriteLine("CMP         dest, sorg");
                        Console.WriteLine("JMP         label");
                        Console.WriteLine("JA / JNBE   label (se maggiore)");
                        Console.WriteLine("JAE / JNB   label (se maggiore o uguale)");
                        Console.WriteLine("JB / JNAE   label (se minore)");
                        Console.WriteLine("JBE / JNA   label (se minore o uguale)");
                        Console.WriteLine("JE / JZ     label (se uguale)");
                        Console.WriteLine("JG / JNLE   label (se maggiore)");
                        Console.WriteLine("JGE / JNL   label (se maggiore o uguale)");
                        Console.WriteLine("JLE / JNG   label (se minore o uguale)");
                        Console.WriteLine("JNE / JNZ   label (se diverso)");
                        Console.WriteLine("CALL        label");
                        Console.WriteLine("RET         //");


                        Console.WriteLine("\n\nPremi un tasto qualsiasi per uscire...");

                        Console.SetCursorPosition(0, 0);

                        Console.ReadKey(true);
                    }
                    else if (scelta == "5")
                    {
                        File.WriteAllLines(localAppDataDOSBox, backupFile);

                        Environment.Exit(0);
                    }
                }
                while (scelta != "1" && scelta != "2" && scelta != "3");

                Console.Clear();

                if (!debug)
                {
                    if (scelta != "2")
                    {
                        Console.Write("Inserire il nome del file (senza estensione) da creare e poi copiare in TASM: ");
                        nome = Console.ReadLine();
                    }
                    else
                    {
                        Console.Write("Inserire il nome del file (senza estensione) da copiare in TASM: ");
                        nome = Console.ReadLine();
                    }
                }
                else
                {
                    Console.Write("Inserire il nome del file (senza estensione) di cui effettuare il debug con Turbo Debugger: ");
                    nome = Console.ReadLine();
                }

                if (!debug)
                {
                    if (scelta != "2")
                    {
                        Console.Clear();

                        assemblyPath = documentsPath + $"\\Assembly Projects\\{nome}.asm";

                        File.WriteAllText(assemblyPath, demoText);

                        Console.WriteLine($"File {nome} creato in {assemblyPath}\n");

                        Process.Start(assemblyPath);

                        Console.WriteLine("File aperto con successo");

                        Console.WriteLine("\nUna volta finito di scrivere il file, torna qui e premi un tasto qualsiasi per continuare il processo...");


                        Console.WriteLine("\n\nSe hai cambiato idea e vuoi tornare indietro premi B");

                        if (Console.ReadKey(true).Key == ConsoleKey.B)
                        {
                            goBack = true;
                        }

                        if (!goBack)
                        {
                            Console.Clear();

                            do
                            {
                                Console.Clear();

                                Console.Write($"Inserire un nome corto (massimo 8 caratteri) per il file {nome}.asm: ");
                                nomeCorto = Console.ReadLine();
                            }
                            while (nomeCorto.Length > 8);

                            TASMPath = $"C:\\TASM\\TASM\\{nomeCorto}.asm";

                            File.Copy(assemblyPath, TASMPath, true);

                            Console.Clear();

                            Console.WriteLine($"Hai copiato: {nome}.asm in TASM, rinominandolo: {nomeCorto}.asm");

                            editLines[editLinesLenght + 5] = $"tasm {nomeCorto}.asm";
                            editLines[editLinesLenght + 6] = $"tlink {nomeCorto}.obj";
                            editLines[editLinesLenght + 7] = $"{nomeCorto}";

                            File.WriteAllLines(localAppDataDOSBox, editLines);

                            Process.Start(DOSBoxPath);

                            Thread.Sleep(5000);
                        }
                    }
                    else
                    {
                        assemblyPath = documentsPath + $"\\Assembly Projects\\{nome}.asm";

                        do
                        {
                            Console.Clear();

                            Console.Write($"Inserire un nome corto (massimo 8 caratteri) per il file {nome}.asm: ");
                            nomeCorto = Console.ReadLine();
                        }
                        while (nomeCorto.Length > 8);

                        TASMPath = $"C:\\TASM\\TASM\\{nomeCorto}.asm";

                        File.Copy(assemblyPath, TASMPath, true);

                        Console.Clear();

                        Console.WriteLine($"Hai copiato: {nome}.asm in TASM, rinominandolo: {nomeCorto}.asm");

                        editLines[editLinesLenght + 5] = $"tasm {nomeCorto}.asm";
                        editLines[editLinesLenght + 6] = $"tlink {nomeCorto}.obj";
                        editLines[editLinesLenght + 7] = $"{nomeCorto}";

                        File.WriteAllLines(localAppDataDOSBox, editLines);

                        Process.Start(DOSBoxPath);

                        Thread.Sleep(5000);
                    }
                }
                else
                {
                    editLines[editLinesLenght + 5] = $"tasm {nome}.asm";
                    editLines[editLinesLenght + 6] = $"tlink {nome}.obj";
                    editLines[editLinesLenght + 7] = $"td {nome}";

                    File.WriteAllLines(localAppDataDOSBox, editLines);

                    Process.Start(DOSBoxPath);

                    Thread.Sleep(5000);
                }
            }
            while (true);
        }
    }
}
//Alex Sandri