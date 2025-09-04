/**
 * Formata um número brasileiro de telefone.
 * Aceita entradas "bagunçadas" (com +55, espaços, pontuação) e devolve uma máscara legível.
 * - 11 dígitos => (AA) NNNNN-NNNN  (celular com DDD, ex.: (11) 91234-5678)
 * - 10 dígitos => (AA) NNNN-NNNN   (fixo com DDD, ex.: (11) 1234-5678)
 * -  9 dígitos => NNNNN-NNNN       (celular sem DDD)
 * -  8 dígitos => NNNN-NNNN        (fixo sem DDD)
 * Caso não reconheça o padrão, retorna o valor original.
 *
 * @param {string | null | undefined} value
 * @returns {string}
 */
export function formatBrPhone(value) {
  // Converte para string garantindo que null/undefined virem string vazia
  const raw = String(value ?? "");

  // Remove tudo que NÃO for dígito (mantém zeros à esquerda do número)
  // Situação: usuário colou "(11) 91234-5678", "+55 11 91234-5678", etc.
  let digits = raw.replace(/\D+/g, "");

  // Se não sobrou nenhum dígito, não há o que formatar → retorna original
  if (!digits) return raw;

  // Normaliza código do país "+55"
  // Situação: usuário colou com DDI, ex.: "+55 (11) 91234-5678"
  // 13 dígitos (55 + 11 dígitos) ou 12 (55 + 10 dígitos) → remove o "55"
  if (
    (digits.length === 13 || digits.length === 12) &&
    digits.startsWith("55")
  ) {
    digits = digits.slice(2);
  }

  // Remove prefixo tronco "0" usado em alguns contextos de discagem (ex.: 0 + DDD)
  // Situação: "011912345678" ou "01112345678" → vira "11912345678" / "1112345678"
  if ((digits.length === 12 || digits.length === 11) && digits[0] === "0") {
    digits = digits.replace(/^0+/, "");
  }

  // Após normalizações, decide o formato pelo total de dígitos
  const len = digits.length;

  if (len === 11) {
    // 11 dígitos: padrão CELULAR com DDD → (AA) 9XXXX-XXXX
    // Situação: "11912345678" → "(11) 91234-5678"
    return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(
      7,
      11
    )}`;
  }

  if (len === 10) {
    // 10 dígitos: padrão FIXO com DDD → (AA) XXXX-XXXX
    // Situação: "1112345678" → "(11) 1234-5678"
    return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(
      6,
      10
    )}`;
  }

  if (len === 9) {
    // 9 dígitos: CELULAR sem DDD → NNNNN-NNNN
    // Situação: "912345678" → "91234-5678"
    return `${digits.slice(0, 5)}-${digits.slice(5, 9)}`;
  }

  if (len === 8) {
    // 8 dígitos: FIXO sem DDD → NNNN-NNNN
    // Situação: "12345678" → "1234-5678"
    return `${digits.slice(0, 4)}-${digits.slice(4, 8)}`;
  }

  // Comprimento inesperado: não tenta adivinhar → devolve o texto original
  // Situação: números muito curtos/longos ou com códigos não padronizados
  return raw;
}
