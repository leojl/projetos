#include <stdio.h>
#include <stdlib.h>
#include <omp.h>
#include <time.h>


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

double** liberarMatriz(double **matriz, int tamanho){
   int i;
  
   for(i=0; i<tamanho; i++){
      free(matriz[i]); //Desalocando as colunas
   }

   free(matriz);//Desalocando o numero de linhas

   return NULL;
}


void inserirNumeroAleatorios(double **matriz, int tamanho){
   int i, j;

   srand(time(NULL));
  //#pragma omp parallel for shared(matriz,tamanho) private(i,j)
   for(i=0; i<tamanho; i++){
      for(j=0; j<tamanho; j++){
	     matriz[i][j] = rand() % 11;
	  }
   }
}

void mostrarMatriz(FILE *arquivoSaida, double **matriz, int tamanho){
   int i, j;
  //#pragma omp parallel for shared(matriz,tamanho) private(i,j)
   for(i=0; i<tamanho; i++){
      for(j=0; j<tamanho; j++){
	     fprintf(arquivoSaida, "%.2lf ", matriz[i][j]);
	  }
	  fprintf(arquivoSaida, "\n");
   }

}

double** matrizQuadrada(double **matrizPrincipal, double **MatrizPrincipal, int tamanho){
   int i, j, k;
   double somaTemp, **matrizAuxiliar=NULL;

   matrizAuxiliar = criarMatriz(matrizAuxiliar, tamanho);
  //#pragma omp parallel for schedule(guided) shared(matrizPrincipal,matrizAuxiliar) private(i,j,k,somaTemp) num_threads(4)
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

return matrizPrincipal;
}

double** multiplicacaoMatrizPorEscalar(double **matriz, double escalar, int tamanho){
   int i, j;
  //#pragma omp parallel for shared(matriz,tamanho,escalar) private(i,j)
   for(i=0; i<tamanho; i++){
      for(j=0; j<tamanho; j++){
	     matriz[i][j] *= escalar;
	  }
   }
return matriz;
}

double** somaMatrizes(double **matriz1, double **matriz2, double **matrizSoma, int tamanho){
   int i, j;
  //#pragma omp parallel for shared(matriz1, matriz2, matrizSoma, tamanho) private(i ,j)
   for(i=0; i<tamanho; i++){
      for(j=0; j<tamanho; j++){
	     matrizSoma[i][j] = matriz1[i][j] + matriz2[i][j];
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
   else
      scanf("%d", &tamanho);
   
	tempoInicio = omp_get_wtime();
   A = criarMatriz(A, tamanho);
   B = criarMatriz(B, tamanho);
   C = criarMatriz(C, tamanho); //Matriz auxiliar para fazer 2*A^2
   D = criarMatriz(D, tamanho); //Matriz auxiliar para fazer 3*A


   /*Fazer B = 2*A^2 + 3*A */
   inserirNumeroAleatorios(A, tamanho);

   arquivoSaida = fopen("MatrizResultanteSerial.txt", "w");
   
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
	
	printf("Tempo de Execucao: %lf", tempoFim - tempoInicio);
	
   if(tamanho <= 20){
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
   }

//fclose(arquivoSaida);

	arquivoSaida = NULL;
	return 0;
}
