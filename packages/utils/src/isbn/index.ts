export function convertToIsbn10(isbn13: string): string {
  if (isbn13.length !== 13) {
    throw new Error('13 桁の ISBN をハイフンなしで指定する必要があります')
  }

  // 先頭の 3 桁を削除 & 末尾の 1 桁 (チェックディジット) を削除
  const source = isbn13.slice(3, 12)

  const sum = source
    // 先頭から 1 文字ずつ取り出し、
    .split('')
    // 10, 9, 8, ..., 2 と掛け算し、
    .map((s, i) => Number(s) * (10 - i))
    // その合計を取る
    .reduce((p, c) => p + c)

  // 合計を 11 で割った余りを求める
  const remainder = 11 - (sum % 11)

  // 余りの値に応じたチェックディジットを付加し、ISBN 10 として返却する
  switch (remainder) {
    case 10: {
      const checkDigit = 'X'
      return `${source}${checkDigit}`
    }
    case 11: {
      const checkDigit = '0'
      return `${source}${checkDigit}`
    }
    default: {
      const checkDigit = `${remainder}`
      return `${source}${checkDigit}`
    }
  }
}
