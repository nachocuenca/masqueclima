import Image from "next/image";
import { site } from "@/content/site";

type WhatsAppFloatingProps = {
  label: string;
};

export function WhatsAppFloating({ label }: WhatsAppFloatingProps) {
  return (
    <a
      aria-label={label}
      className="focus-ring fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-full bg-[#25d366] px-4 py-3 text-sm font-bold text-white shadow-lg shadow-neutral-900/20 transition hover:translate-y-[-1px]"
      href={site.whatsappUrl}
      rel="noopener noreferrer"
      target="_blank"
    >
      <span className="hidden sm:inline">{label}</span>
      <Image
        alt=""
        aria-hidden="true"
        height={20}
        src="/assets/img/WhatsApp.png"
        width={20}
      />
    </a>
  );
}
