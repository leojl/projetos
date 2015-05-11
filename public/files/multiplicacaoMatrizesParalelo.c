#include <stdio.h>
#include <stdlib.h>
#include <time.h>
#include <omp.h>


double** criarMatriz(double **matriz, int tamanho){
   int i, j;

   matriz = (double**)malloc(sizeof(double)*tamanho); //Alocando o numero de linhas

   for(i=0; i<tamanho; i++){
      matriz[i] = (double*)malloc(sizeof(double)*tamanho); //Alocando o numero de colunas
   }

   for(i=0; i<tamanho; i++){
      for(j=0; j<tamanho; j++){
	     matriz[i][j] = 0;
	  }
   }
return matriz;
}

void liberarMatriz(double **matriz, int tamanho){
   int i;
  
   for(i=0; i<tamanho; i++){
      free(matriz[i]); //Desalocando as colunas
   }

   free(matriz);//Desalocando o numero de linhas
   matriz = NULL;

}

void inserirNumeroAleatorios(double **matriz, int tamanho){
   int i, j;
   int quantidadeCores;

   quantidadeCores = omp_get_num_procs();
   //omp_set_num_threads(quantidadeCores);

   srand(time(NULL));

	#pragma omp parallel num_threads(quantidadeCores)
	{
		#pragma omp for schedule(static, quantidadeCores)
		
		   for(i=0; i<tamanho; i++){
			   for(j=0; j<tamanho; j++){
			      matriz[i][j] = rand() % 11;
				}
		   }
		
   }
}

void mostrarMatriz(FILE *arquivoSaida, double **matriz, int tamanho){
   int i, j;
   int quantidadeCores;

   quantidadeCores = omp_get_num_procs();
   omp_set_num_threads(quantidadeCores);
   
   #pragma omp parallel num_threads(quantidadeCores)
	{
	   #pragma omp for schedule(static, quantidadeCores)
	      for(i=0; i<tamanho; i++){
            for(j=0; j<tamanho; j++){
	            fprintf(arquivoSaida, "%.2lf ", matriz[i][j]);
	         }
	         fprintf(arquivoSaida, "\n");
         }
      
   }
}

double** matrizQuadrada(double **matrizPrincipal, double **MatrizPrincipal, int tamanho){
   int i, j, k;
   double somaTemp, **matrizAuxiliar=NULL;
   
   int quantidadeCores;

   quantidadeCores = omp_get_num_procs();
   omp_set_num_threads(quantidadeCores);

   matrizAuxiliar = criarMatriz(matrizAuxiliar, tamanho);
   #pragma omp parallel num_threads(quantidadeCores)
	{
	  #pragma omp for private(j,k,somaTemp)
	     for(i=0; i<tamanho; i++){
               for(j=0; j<tamanho; j++){
	           somaTemp = 0;
		        for(k=0; k<tamanho; k++){
	                   somaTemp += matrizPrincipal[i][k] * matrizPrincipal[k][j];
		         }
		         matrizAuxiliar[i][j] = somaTemp;
	         }
         }
     

   matrizPrincipal = matrizAuxiliar;
   }
return matrizPrincipal;
}

double** multiplicacaoMatrizPorEscalar(double **matriz, double escalar, int tamanho){
   int i, j;
   int quantidadeCores;

   quantidadeCores = omp_get_num_procs();
   omp_set_num_threads(quantidadeCores);   

   #pragma omp parallel num_threads(quantidadeCores)
   {
      #pragma omp for schedule(static, quantidadeCores)
		for(i=0; i<tamanho; i++){
         for(j=0; j<tamanho; j++){
	         matriz[i][j] *= escalar;
	      }
   }
}
return matriz;
}

double** somaMatrizes(double **matriz1, double **matriz2, double **matrizSoma, int tamanho){
   int i, j;
   int quantidadeCores;

   quantidadeCores = omp_get_num_procs();
   omp_set_num_threads(quantidadeCores); 

   #pragma omp parallel num_threads(quantidadeCores)
   {
		#pragma omp for schedule (static, quantidadeCores)
			for(i=0; i<tamanho; i++){
				for(j=0; j<tamanho; j++){
				  matrizSoma[i][j] = matriz1[i][j] + matriz2[i][j];
			  }
			}
	}

return matrizSoma;
}

int main(int argc, char *argv[]){
   FILE *arquivoSaida=NULL;
   double **A=NULL, **B=NULL, **C=NULL, **D=NULL;
   double tempoInicio, tempoFim;
	int tamanho;

   if(argc > 1)
      tamanho = atoi(argv[1]);
   else{
      printf("Tamanho da Matriz: ");
		scanf("%d", &tamanho);
	}

	tempoInicio = omp_get_wtime();	
	A = criarMatriz(A, tamanho);
	B = criarMatriz(B, tamanho);
	C = criarMatriz(C, tamanho); //Matriz auxiliar para fazer 2*A^2 
	D = criarMatriz(D, tamanho); //Matriz auxiliar para fazer 3*A
   
	/*Fazer B = 2*A^2 + 3*A */
   inserirNumeroAleatorios(A, tamanho);
    
   arquivoSaida = fopen("MatrizResultanteParalelo.txt", "w");
   
   fprintf(arquivoSaida, "Matriz A\n");
   mostrarMatriz(arquivoSaida, A, tamanho);
   fprintf(arquivoSaida, "\n");   

   C = A;
   D = A;
   
   C = matrizQuadrada(C, C, tamanho);
	C = multiplicacaoMatrizPorEscalar(C, 2, tamanho);
	D = multiplicacaoMatrizPorEscalar(D, 3, tamanho);
	B = somaMatrizes(C, D, B, tamanho);
	tempoFim = omp_get_wtime();
	
	printf("Tempo de Execucao: %lf\n", tempoFim - tempoInicio);
	
   fprintf(arquivoSaida, "Matriz C, onde C = 2*A^2\n");
   mostrarMatriz(arquivoSaida, C, tamanho);
   fprintf(arquivoSaida, "\n");

   fprintf(arquivoSaida, "Matriz D, onde D = 3*A\n");
   mostrarMatriz(arquivoSaida, D, tamanho);
   fprintf(arquivoSaida, "\n");

   fprintf(arquivoSaida, "Matriz B, onde B = C + D\n");
   mostrarMatriz(arquivoSaida, B, tamanho);
   fprintf(arquivoSaida, "\n");
   
	fprintf(arquivoSaida, "Tempo de Execucao: %lf\n", tempoFim - tempoInicio);

//fclose(arquivoSaida);

A = NULL;
B = NULL;
C = NULL;
D = NULL;
arquivoSaida = NULL;

return 0;
}
