import { format } from "date-fns";
console.log(format(new Date(1984, 8, 17), "yyyy年M月d日 HH:mm:ss"));
console.log(format(new Date(1984, 8, 17), "yyyy年M月d日"));
console.log(format(new Date(1984, 8, 17), "MMMM 执行, yyyy @ HH:mm"));
